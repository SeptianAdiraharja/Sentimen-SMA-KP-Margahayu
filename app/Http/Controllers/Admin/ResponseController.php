<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\QuestionnaireResponse;
use App\Models\Period;
use App\Models\Question;
use App\Models\Aspect;
use App\Models\ResponseAnswer;
use App\Services\NaiveBayesService;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ResponseController extends Controller
{
    public function index(Request $request)
    {
        $periodId = $request->input('period_id');
        $rating = $request->input('rating');
        $search = $request->input('search');

        $periods = Period::orderBy('id', 'desc')->get();

        $query = QuestionnaireResponse::with(['period', 'answers.aspect', 'answers.question'])
            ->orderBy('id', 'desc');

        if ($periodId) {
            $query->where('period_id', $periodId);
        }

        if ($rating) {
            $query->where('overall_rating', $rating);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('respondent_code', 'like', "%{$search}%")
                  ->orWhereHas('answers', fn($aq) => $aq->where('raw_answer', 'like', "%{$search}%"));
            });
        }

        $responses = $query->paginate(15)->withQueryString();

        return view('admin.responses.index', compact('responses', 'periods', 'periodId', 'rating', 'search'));
    }

    public function show(QuestionnaireResponse $response)
    {
        $response->load(['period', 'answers.question', 'answers.aspect']);
        return view('admin.responses.show', compact('response'));
    }

    public function destroy(QuestionnaireResponse $response)
    {
        $response->delete();
        return redirect()->route('admin.responses.index')->with('success', 'Data responden berhasil dihapus.');
    }

    public function import(Request $request, NaiveBayesService $nb)
    {
        $request->validate([
            'period_id' => 'required|exists:periods,id',
            'excel_file' => 'nullable|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $periodId = $request->input('period_id');
        
        // Cek apakah user mengunggah file atau menggunakan file default yang ada di folder vrillia
        if ($request->hasFile('excel_file')) {
            $filePath = $request->file('excel_file')->getRealPath();
        } else {
            $defaultPath = base_path('../data kuesioner.xlsx');
            if (!file_exists($defaultPath)) {
                $defaultPath = base_path('../perancangan/data kuesioner.xlsx');
            }
            if (!file_exists($defaultPath)) {
                return back()->with('error', 'File Excel tidak ditemukan. Silakan pilih file untuk diunggah.');
            }
            $filePath = $defaultPath;
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        if (count($rows) <= 1) {
            return back()->with('error', 'File Excel kosong atau hanya memiliki header.');
        }

        $headers = $rows[0];
        $aspects = Aspect::all()->keyBy('code');

        // Pemetaan kata kunci untuk menentukan aspek jika pertanyaan belum ada di database
        $aspectKeywordMap = [
            'cita_rasa' => ['cita rasa', 'rasa', 'hambar', 'enak', 'gurih', 'lezat'],
            'porsi' => ['porsi', 'kebutuhan makan siang', 'banyak', 'sedikit', 'kenyang'],
            'variasi' => ['variasi', 'variatif', 'berganti', 'kreatif', 'menu makanan yang diberikan setiap hari'],
            'kebersihan' => ['kebersihan', 'penyajian', 'kemasan', 'tempat makan', 'higienis'],
            'kesegaran' => ['kesegaran', 'segar', 'bahan makanan', 'sayuran', 'lauk', 'buah'],
            'masalah' => ['masalah', 'kendala', 'pernah menemukan masalah'],
            'hal_disukai' => ['disukai', 'paling disukai', 'menu favorit', 'paling anda sukai'],
            'saran' => ['saran anda agar kualitas', 'agar kualitas menu', 'rekomendasi'],
            'kesimpulan' => ['kritik', 'kesimpulan', 'program mbg secara keseluruhan', 'tuliskan kritik'],
        ];

        $newQuestionsCount = 0;
        $questionMapping = []; // index kolom => Question Model

        foreach ($headers as $colIdx => $headerText) {
            $cleanHeaderText = trim((string)$headerText);
            if (empty($cleanHeaderText)) continue;

            // Cari apakah pertanyaan dengan teks serupa/nomor sudah ada di database
            $question = Question::where('question_text', $cleanHeaderText)
                ->orWhere('question_number', $colIdx + 1)
                ->first();

            $lowerText = strtolower($cleanHeaderText);
            $isRating = (str_contains($lowerText, 'bagaimana kualitas menu') || str_contains($lowerText, 'rating') || $colIdx === 9)
                && !str_contains($lowerText, 'kritik')
                && !str_contains($lowerText, 'tuliskan');

            if (!$question) {
                $matchedAspectId = null;
                if (!$isRating) {
                    foreach ($aspectKeywordMap as $code => $keywords) {
                        foreach ($keywords as $kw) {
                            if (str_contains($lowerText, $kw)) {
                                $matchedAspectId = $aspects[$code]->id ?? null;
                                break 2;
                            }
                        }
                    }
                }

                // Simpan pertanyaan baru yang ada di Excel ke database
                $question = Question::create([
                    'aspect_id' => $matchedAspectId,
                    'question_number' => $colIdx + 1,
                    'question_text' => $cleanHeaderText,
                    'type' => $isRating ? 'rating' : 'essay',
                    'is_active' => true,
                ]);

                $newQuestionsCount++;
            } else {
                // Pastikan pertanyaan nomor 9 memiliki aspect_id aspek 'kesimpulan' dan bertipe essay
                if ($question->question_number === 9) {
                    $question->type = 'essay';
                    if (empty($question->aspect_id)) {
                        $question->aspect_id = $aspects['kesimpulan']->id ?? null;
                    }
                    $question->save();
                } elseif ($question->question_number === 10) {
                    $question->type = 'rating';
                    $question->save();
                }
            }

            $questionMapping[$colIdx] = $question;
        }

        $nb->train();
        $importedCount = 0;
        $existingCount = QuestionnaireResponse::where('period_id', $periodId)->count();

        $ratingMap = [
            'sangat puas' => ['label' => 'Sangat Puas', 'score' => 5],
            'cukup puas' => ['label' => 'Cukup Puas', 'score' => 4],
            'cukup pus' => ['label' => 'Cukup Puas', 'score' => 4],
            'kurang puas' => ['label' => 'Kurang Puas', 'score' => 3],
            'tidak puas' => ['label' => 'Tidak Puas', 'score' => 2],
            'sangat tidak puas' => ['label' => 'Sangat Tidak Puas', 'score' => 1],
        ];

        // Temukan indeks kolom rating (khusus yang bertipe 'rating')
        $ratingColIdx = null;
        foreach ($questionMapping as $idx => $qModel) {
            if ($qModel->type === 'rating') {
                $ratingColIdx = $idx;
                break;
            }
        }
        if ($ratingColIdx === null && isset($headers[9])) {
            $ratingColIdx = 9;
        }

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            if (empty(array_filter($row))) continue;

            $ratingRaw = $ratingColIdx !== null ? trim((string)($row[$ratingColIdx] ?? 'Cukup Puas')) : 'Cukup Puas';
            $lookup = strtolower($ratingRaw);
            $overallRating = $ratingMap[$lookup]['label'] ?? 'Cukup Puas';
            $overallScore = $ratingMap[$lookup]['score'] ?? 4;

            $respondentCode = 'RESP-' . str_pad($existingCount + $importedCount + 1, 3, '0', STR_PAD_LEFT);

            $resp = QuestionnaireResponse::create([
                'period_id' => $periodId,
                'respondent_code' => $respondentCode,
                'overall_rating' => $overallRating,
                'overall_rating_score' => $overallScore,
            ]);

            foreach ($questionMapping as $colIdx => $qModel) {
                // Lewati kolom rating pada jawaban esai
                if ($colIdx === $ratingColIdx || $qModel->type === 'rating') {
                    continue;
                }

                $rawText = trim((string)($row[$colIdx] ?? ''));
                if (empty($rawText)) continue;

                $predict = $nb->predict($rawText, $qModel->aspect_id);

                ResponseAnswer::create([
                    'response_id' => $resp->id,
                    'question_id' => $qModel->id,
                    'aspect_id' => $qModel->aspect_id,
                    'raw_answer' => $rawText,
                    'clean_answer' => $predict['preprocessing']['clean'],
                    'tokens' => $predict['preprocessing']['tokens'],
                    'filtered_tokens' => $predict['preprocessing']['filtered_tokens'],
                    'stemmed_text' => $predict['preprocessing']['stemmed_text'],
                    'sentiment' => $predict['sentiment'],
                    'confidence_score' => $predict['confidence'],
                    'sentiment_details' => $predict['scores'],
                ]);
            }

            $importedCount++;
        }

        $message = "Berhasil mengimpor {$importedCount} data responden.";
        if ($newQuestionsCount > 0) {
            $message .= " Ditemukan dan ditambahkan {$newQuestionsCount} pertanyaan baru dari file Excel ke dalam database.";
        }

        return redirect()->route('admin.responses.index')->with('success', $message);
    }
}

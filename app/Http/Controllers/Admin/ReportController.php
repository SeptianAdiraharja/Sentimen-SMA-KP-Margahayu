<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Period;
use App\Models\Aspect;
use App\Models\QuestionnaireResponse;
use App\Models\ResponseAnswer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $periodId = $request->input('period_id');
        $periods = Period::orderBy('id', 'desc')->get();

        $activePeriod = $periodId ? Period::find($periodId) : Period::where('is_active', true)->first();
        if (!$activePeriod) $activePeriod = Period::latest()->first();

        $periodId = $activePeriod?->id;

        $reportData = $this->buildReportData($periodId);

        return view('admin.reports.index', array_merge($reportData, [
            'periods' => $periods,
            'activePeriod' => $activePeriod,
        ]));
    }

    public function print(Request $request)
    {
        $periodId = $request->input('period_id');
        $activePeriod = $periodId ? Period::find($periodId) : Period::where('is_active', true)->first();
        if (!$activePeriod) $activePeriod = Period::latest()->first();

        $reportData = $this->buildReportData($activePeriod?->id);

        return view('admin.reports.print', array_merge($reportData, [
            'activePeriod' => $activePeriod,
        ]));
    }

    public function exportExcel(Request $request)
    {
        $periodId = $request->input('period_id');
        $activePeriod = $periodId ? Period::find($periodId) : Period::where('is_active', true)->first();
        if (!$activePeriod) $activePeriod = Period::latest()->first();

        $reportData = $this->buildReportData($activePeriod?->id);

        $spreadsheet = new Spreadsheet();

        // Sheet 1: Ringkasan Aspek MBG
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Ringkasan Sentimen Aspek');

        $sheet->setCellValue('A1', 'LAPORAN EVALUASI KUALITAS MENU MAKAN BERGIZI GRATIS (MBG)');
        $sheet->setCellValue('A2', 'SMA KARYA PEMBANGUNAN MARGAHAYU');
        $sheet->setCellValue('A3', 'Periode: ' . ($activePeriod ? $activePeriod->name : 'Semua Periode'));
        $sheet->setCellValue('A4', 'Tanggal Cetak: ' . now()->format('d-m-Y H:i'));

        $sheet->setCellValue('A6', 'No');
        $sheet->setCellValue('B6', 'Aspek Kualitas Menu');
        $sheet->setCellValue('C6', 'Total Jawaban');
        $sheet->setCellValue('D6', 'Positif');
        $sheet->setCellValue('E6', 'Positif (%)');
        $sheet->setCellValue('F6', 'Netral');
        $sheet->setCellValue('G6', 'Netral (%)');
        $sheet->setCellValue('H6', 'Negatif');
        $sheet->setCellValue('I6', 'Negatif (%)');
        $sheet->setCellValue('J6', 'Status Prioritas');

        $row = 7;
        $no = 1;
        foreach ($reportData['aspectMetrics'] as $m) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $m['name']);
            $sheet->setCellValue('C' . $row, $m['total']);
            $sheet->setCellValue('D' . $row, $m['positif']);
            $sheet->setCellValue('E' . $row, $m['positif_pct'] . '%');
            $sheet->setCellValue('F' . $row, $m['netral']);
            $sheet->setCellValue('G' . $row, $m['netral_pct'] . '%');
            $sheet->setCellValue('H' . $row, $m['negatif']);
            $sheet->setCellValue('I' . $row, $m['negatif_pct'] . '%');
            $status = $m['total'] == 0 ? 'Belum Ada Data' : ($m['negatif_pct'] >= 30 ? 'Perlu Perbaikan Segera' : ($m['negatif_pct'] >= 15 ? 'Perlu Perhatian' : 'Kualitas Baik'));
            $sheet->setCellValue('J' . $row, $status);
            $row++;
        }

        // Sheet 2: Data Responden & Jawaban Esai
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Data Jawaban & Sentimen');
        $sheet2->setCellValue('A1', 'Kode Responden');
        $sheet2->setCellValue('B1', 'Rating Keseluruhan');
        $sheet2->setCellValue('C1', 'Aspek');
        $sheet2->setCellValue('D1', 'Jawaban Siswa');
        $sheet2->setCellValue('E1', 'Hasil Stemming (NLP)');
        $sheet2->setCellValue('F1', 'Klasifikasi Sentimen');
        $sheet2->setCellValue('G1', 'Confidence');

        $answers = ResponseAnswer::with(['response', 'aspect'])
            ->when($activePeriod?->id, fn($q) => $q->whereHas('response', fn($rq) => $rq->where('period_id', $activePeriod->id)))
            ->get();

        $r2 = 2;
        foreach ($answers as $ans) {
            $sheet2->setCellValue('A' . $r2, $ans->response?->respondent_code);
            $sheet2->setCellValue('B' . $r2, $ans->response?->overall_rating);
            $sheet2->setCellValue('C' . $r2, $ans->aspect?->name);
            $sheet2->setCellValue('D' . $r2, $ans->raw_answer);
            $sheet2->setCellValue('E' . $r2, $ans->stemmed_text);
            $sheet2->setCellValue('F' . $r2, $ans->sentiment);
            $sheet2->setCellValue('G' . $r2, round($ans->confidence_score * 100, 1) . '%');
            $r2++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Laporan_Evaluasi_MBG_' . date('Ymd_His') . '.xlsx';

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    protected function buildReportData(?int $periodId): array
    {
        $totalResponses = QuestionnaireResponse::when($periodId, fn($q) => $q->where('period_id', $periodId))->count();
        $avgSatisfaction = QuestionnaireResponse::when($periodId, fn($q) => $q->where('period_id', $periodId))->avg('overall_rating_score');

        $aspects = Aspect::with(['responseAnswers' => function ($q) use ($periodId) {
            if ($periodId) {
                $q->whereHas('response', fn($rq) => $rq->where('period_id', $periodId));
            }
        }])->orderBy('sort_order')->get();

        $aspectMetrics = [];
        $recommendations = [];

        foreach ($aspects as $asp) {
            $pos = $asp->responseAnswers->where('sentiment', 'Positif')->count();
            $neu = $asp->responseAnswers->where('sentiment', 'Netral')->count();
            $neg = $asp->responseAnswers->where('sentiment', 'Negatif')->count();
            $total = $pos + $neu + $neg;

            $posPct = $total > 0 ? round(($pos / $total) * 100, 1) : 0;
            $neuPct = $total > 0 ? round(($neu / $total) * 100, 1) : 0;
            $negPct = $total > 0 ? round(($neg / $total) * 100, 1) : 0;

            $aspectMetrics[] = [
                'name' => $asp->name,
                'code' => $asp->code,
                'total' => $total,
                'positif' => $pos,
                'netral' => $neu,
                'negatif' => $neg,
                'positif_pct' => $posPct,
                'netral_pct' => $neuPct,
                'negatif_pct' => $negPct,
            ];

            if ($negPct >= 20) {
                $recommendations[] = [
                    'aspect' => $asp->name,
                    'neg_pct' => $negPct,
                    'notes' => "Tingkat ketidakpuasan mencapai {$negPct}%. Perlu peninjauan standar pengolahan dan pemilihan vendor SPPG.",
                ];
            }
        }

        return [
            'totalResponses' => $totalResponses,
            'avgSatisfaction' => round((float)$avgSatisfaction, 2),
            'aspectMetrics' => $aspectMetrics,
            'recommendations' => $recommendations,
        ];
    }
}

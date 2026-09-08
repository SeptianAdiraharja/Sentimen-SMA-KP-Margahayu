<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Period;
use App\Models\Question;
use App\Models\QuestionnaireResponse;
use App\Models\ResponseAnswer;
use App\Services\NaiveBayesService;

class QuestionnaireController extends Controller
{
    public function index()
    {
        $activePeriod = Period::where('is_active', true)->latest()->first();

        $questions = Question::where('is_active', true)
            ->with('aspect')
            ->orderBy('question_number')
            ->get();

        return view('questionnaire.index', compact('activePeriod', 'questions'));
    }

    public function store(Request $request, NaiveBayesService $nb)
    {
        $activePeriod = Period::where('is_active', true)->latest()->first();
        if (!$activePeriod) {
            return redirect()->back()->with('error', 'Saat ini tidak ada periode kuesioner yang aktif.');
        }

        $request->validate([
            'answers' => 'required|array',
            'overall_rating' => 'required|string',
        ]);

        $ratingScores = [
            'Sangat Puas' => 5,
            'Cukup Puas' => 4,
            'Kurang Puas' => 3,
            'Tidak Puas' => 2,
            'Sangat Tidak Puas' => 1,
        ];

        $overallRating = $request->input('overall_rating');
        $overallScore = $ratingScores[$overallRating] ?? 4;

        $respondentCount = QuestionnaireResponse::where('period_id', $activePeriod->id)->count() + 1;
        $respondentCode = 'RESP-' . str_pad($respondentCount, 3, '0', STR_PAD_LEFT);

        $response = QuestionnaireResponse::create([
            'period_id' => $activePeriod->id,
            'respondent_code' => $respondentCode,
            'overall_rating' => $overallRating,
            'overall_rating_score' => $overallScore,
        ]);

        // Simpan jawaban esai dan lakukan klasifikasi sentimen otomatis
        $questions = Question::where('is_active', true)->get()->keyBy('id');

        foreach ($request->input('answers', []) as $questionId => $rawText) {
            if (!isset($questions[$questionId])) continue;

            $rawText = trim((string)$rawText);
            if (empty($rawText)) continue;

            $question = $questions[$questionId];
            $predict = $nb->predict($rawText, $question->aspect_id);

            ResponseAnswer::create([
                'response_id' => $response->id,
                'question_id' => $question->id,
                'aspect_id' => $question->aspect_id,
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

        return redirect()->route('questionnaire.success')->with('respondent_code', $respondentCode);
    }

    public function success()
    {
        return view('questionnaire.success');
    }
}

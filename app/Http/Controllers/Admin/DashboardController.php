<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Period;
use App\Models\Aspect;
use App\Models\QuestionnaireResponse;
use App\Models\ResponseAnswer;
use App\Services\NaiveBayesService;

class DashboardController extends Controller
{
    public function index(Request $request, NaiveBayesService $nb)
    {
        $selectedPeriodId = $request->input('period_id');
        $periods = Period::orderBy('id', 'desc')->get();

        $activePeriod = null;
        if ($selectedPeriodId) {
            $activePeriod = Period::find($selectedPeriodId);
        }
        if (!$activePeriod) {
            $activePeriod = Period::where('is_active', true)->first() ?? Period::latest()->first();
        }

        $periodId = $activePeriod?->id;

        // Total Responden & Jawaban
        $totalResponses = QuestionnaireResponse::when($periodId, fn($q) => $q->where('period_id', $periodId))->count();
        $totalAnswers = ResponseAnswer::when($periodId, function ($q) use ($periodId) {
            $q->whereHas('response', fn($rq) => $rq->where('period_id', $periodId));
        })->count();

        // Distribusi Sentimen Keseluruhan
        $sentimentTotals = ResponseAnswer::when($periodId, function ($q) use ($periodId) {
            $q->whereHas('response', fn($rq) => $rq->where('period_id', $periodId));
        })
            ->selectRaw("sentiment, count(*) as count")
            ->groupBy('sentiment')
            ->pluck('count', 'sentiment')
            ->toArray();

        $sentimentCounts = [
            'Positif' => $sentimentTotals['Positif'] ?? 0,
            'Netral' => $sentimentTotals['Netral'] ?? 0,
            'Negatif' => $sentimentTotals['Negatif'] ?? 0,
        ];

        // Distribusi Rating Kepuasan Keseluruhan
        $ratingTotals = QuestionnaireResponse::when($periodId, fn($q) => $q->where('period_id', $periodId))
            ->selectRaw("overall_rating, count(*) as count")
            ->groupBy('overall_rating')
            ->pluck('count', 'overall_rating')
            ->toArray();

        // Rata-rata Skor Kepuasan (1-5)
        $avgSatisfaction = QuestionnaireResponse::when($periodId, fn($q) => $q->where('period_id', $periodId))
            ->avg('overall_rating_score');
        $avgSatisfaction = round((float)$avgSatisfaction, 2);

        // Analisis Sentimen per Aspek
        $aspects = Aspect::with(['responseAnswers' => function ($q) use ($periodId) {
            if ($periodId) {
                $q->whereHas('response', fn($rq) => $rq->where('period_id', $periodId));
            }
        }])->orderBy('sort_order')->get();

        $aspectAnalysis = [];
        $negativeRanking = [];

        foreach ($aspects as $asp) {
            $pos = $asp->responseAnswers->where('sentiment', 'Positif')->count();
            $neu = $asp->responseAnswers->where('sentiment', 'Netral')->count();
            $neg = $asp->responseAnswers->where('sentiment', 'Negatif')->count();
            $total = $pos + $neu + $neg;

            $posPct = $total > 0 ? round(($pos / $total) * 100, 1) : 0;
            $neuPct = $total > 0 ? round(($neu / $total) * 100, 1) : 0;
            $negPct = $total > 0 ? round(($neg / $total) * 100, 1) : 0;

            $aspectAnalysis[] = [
                'id' => $asp->id,
                'code' => $asp->code,
                'name' => $asp->name,
                'total' => $total,
                'positif' => $pos,
                'netral' => $neu,
                'negatif' => $neg,
                'positif_pct' => $posPct,
                'netral_pct' => $neuPct,
                'negatif_pct' => $negPct,
            ];

            $negativeRanking[] = [
                'name' => $asp->name,
                'negatif_count' => $neg,
                'negatif_pct' => $negPct,
                'total' => $total,
            ];
        }

        // Urutkan aspek dengan sentimen negatif tertinggi sebagai prioritas evaluasi
        usort($negativeRanking, fn($a, $b) => $b['negatif_pct'] <=> $a['negatif_pct']);

        // Kata kunci dominan / top terms per sentimen
        $answers = ResponseAnswer::when($periodId, function ($q) use ($periodId) {
            $q->whereHas('response', fn($rq) => $rq->where('period_id', $periodId));
        })->whereNotNull('filtered_tokens')->get();

        $wordFrequencies = [
            'Positif' => [],
            'Negatif' => [],
            'Netral' => [],
        ];

        foreach ($answers as $ans) {
            $s = $ans->sentiment ?? 'Netral';
            $tokens = is_array($ans->filtered_tokens) ? $ans->filtered_tokens : [];
            foreach ($tokens as $t) {
                if (strlen($t) <= 2) continue;
                if (!isset($wordFrequencies[$s][$t])) {
                    $wordFrequencies[$s][$t] = 0;
                }
                $wordFrequencies[$s][$t]++;
            }
        }

        $topWords = [];
        foreach ($wordFrequencies as $cls => $freqs) {
            arsort($freqs);
            $topWords[$cls] = array_slice($freqs, 0, 8, true);
        }

        // Evaluasi Model Naive Bayes
        $modelEvaluation = $nb->evaluateModel();

        return view('admin.dashboard', compact(
            'periods',
            'activePeriod',
            'totalResponses',
            'totalAnswers',
            'sentimentCounts',
            'ratingTotals',
            'avgSatisfaction',
            'aspectAnalysis',
            'negativeRanking',
            'topWords',
            'modelEvaluation'
        ));
    }
}

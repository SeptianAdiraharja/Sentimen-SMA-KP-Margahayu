<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Period;
use App\Models\Aspect;
use App\Models\Question;
use App\Models\ResponseAnswer;
use App\Models\TrainingDataset;
use App\Services\NaiveBayesService;
use App\Services\NlpService;

class AnalysisController extends Controller
{
    public function index(Request $request)
    {
        $aspectId = $request->input('aspect_id');
        $sentiment = $request->input('sentiment');
        $periodId = $request->input('period_id');

        $periods = Period::orderBy('id', 'desc')->get();
        $aspects = Aspect::orderBy('sort_order')->get();

        $query = ResponseAnswer::with(['response.period', 'question', 'aspect'])
            ->orderBy('id', 'desc');

        if ($aspectId) {
            $query->where('aspect_id', $aspectId);
        }

        if ($sentiment) {
            $query->where('sentiment', $sentiment);
        }

        if ($periodId) {
            $query->whereHas('response', fn($q) => $q->where('period_id', $periodId));
        }

        $answers = $query->paginate(20)->withQueryString();

        return view('admin.analysis.index', compact('answers', 'aspects', 'periods', 'aspectId', 'sentiment', 'periodId'));
    }

    public function runAnalysis(Request $request, NaiveBayesService $nb)
    {
        $periodId = $request->input('period_id');
        $aspectId = $request->input('aspect_id');

        $nb->train($aspectId ? (int)$aspectId : null);

        $query = ResponseAnswer::query();
        if ($periodId) {
            $query->whereHas('response', fn($q) => $q->where('period_id', $periodId));
        }
        if ($aspectId) {
            $query->where('aspect_id', $aspectId);
        }

        $answers = $query->get();
        $updatedCount = 0;

        foreach ($answers as $ans) {
            if (empty($ans->raw_answer)) continue;

            $pred = $nb->predict($ans->raw_answer, $ans->aspect_id);

            $ans->update([
                'clean_answer' => $pred['preprocessing']['clean'],
                'tokens' => $pred['preprocessing']['tokens'],
                'filtered_tokens' => $pred['preprocessing']['filtered_tokens'],
                'stemmed_text' => $pred['preprocessing']['stemmed_text'],
                'sentiment' => $pred['sentiment'],
                'confidence_score' => $pred['confidence'],
                'sentiment_details' => $pred['scores'],
            ]);
            $updatedCount++;
        }

        return redirect()->back()->with('success', "Proses analisis sentimen selesai. Total {$updatedCount} jawaban esai telah diproses (Preprocessing, TF-IDF, Naïve Bayes).");
    }

    public function tfidf(Request $request, NaiveBayesService $nb)
    {
        $aspectId = $request->input('aspect_id');
        $periodId = $request->input('period_id');

        $aspects = Aspect::orderBy('sort_order')->get();
        $periods = Period::orderBy('id', 'desc')->get();

        $query = ResponseAnswer::with('aspect')
            ->whereNotNull('stemmed_text');

        if ($aspectId) {
            $query->where('aspect_id', $aspectId);
        }
        if ($periodId) {
            $query->whereHas('response', fn($q) => $q->where('period_id', $periodId));
        }

        $answers = $query->limit(50)->get();

        $docs = [];
        foreach ($answers as $ans) {
            $tokens = explode(' ', trim($ans->stemmed_text));
            $docs[$ans->id] = array_filter($tokens);
        }

        $tfidfData = $nb->calculateTfIdf($docs);

        return view('admin.analysis.tfidf', compact('tfidfData', 'answers', 'aspects', 'periods', 'aspectId', 'periodId'));
    }

    public function modelMetrics(NaiveBayesService $nb)
    {
        $evaluation = $nb->evaluateModel();
        $trainingCount = TrainingDataset::count();
        $datasets = TrainingDataset::with('aspect')->orderBy('id', 'desc')->paginate(15);
        $aspects = Aspect::orderBy('sort_order')->get();

        return view('admin.analysis.metrics', compact('evaluation', 'trainingCount', 'datasets', 'aspects'));
    }

    public function storeDataset(Request $request, NlpService $nlp)
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'label' => 'required|in:Positif,Negatif,Netral',
            'aspect_id' => 'nullable|exists:aspects,id',
        ]);

        $prep = $nlp->preprocess($validated['text']);
        $validated['stemmed_text'] = $prep['stemmed_text'];

        TrainingDataset::create($validated);

        return redirect()->back()->with('success', 'Data latih Naïve Bayes berhasil ditambahkan.');
    }

    public function destroyDataset(TrainingDataset $dataset)
    {
        $dataset->delete();
        return redirect()->back()->with('success', 'Data latih berhasil dihapus.');
    }
}

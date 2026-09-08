<?php

namespace App\Services;

use App\Models\TrainingDataset;

class NaiveBayesService
{
    protected NlpService $nlp;
    protected array $classes = ['Positif', 'Negatif', 'Netral'];
    protected array $classDocCounts = ['Positif' => 0, 'Negatif' => 0, 'Netral' => 0];
    protected array $classWordCounts = ['Positif' => 0, 'Negatif' => 0, 'Netral' => 0];
    protected array $wordFreqPerClass = ['Positif' => [], 'Negatif' => [], 'Netral' => []];
    protected array $vocabulary = [];
    protected int $totalDocs = 0;
    protected bool $isTrained = false;

    public function __construct(NlpService $nlp)
    {
        $this->nlp = $nlp;
    }

    /**
     * Latih model Naive Bayes menggunakan dataset training di database
     * atau kamus default lexicon/training jika DB masih sedikit.
     */
    public function train(?int $aspectId = null): void
    {
        $query = TrainingDataset::query();
        if ($aspectId) {
            $query->where(function ($q) use ($aspectId) {
                $q->where('aspect_id', $aspectId)->orWhereNull('aspect_id');
            });
        }
        $dataset = $query->get();

        // Reset state
        $this->classDocCounts = ['Positif' => 0, 'Negatif' => 0, 'Netral' => 0];
        $this->classWordCounts = ['Positif' => 0, 'Negatif' => 0, 'Netral' => 0];
        $this->wordFreqPerClass = ['Positif' => [], 'Negatif' => [], 'Netral' => []];
        $this->vocabulary = [];
        $this->totalDocs = $dataset->count();

        foreach ($dataset as $row) {
            $label = $row->label;
            if (!in_array($label, $this->classes)) {
                continue;
            }

            $this->classDocCounts[$label]++;

            // Gunakan stemmed_text jika sudah ada, atau proses teks
            if (!empty($row->stemmed_text)) {
                $tokens = explode(' ', trim($row->stemmed_text));
            } else {
                $prep = $this->nlp->preprocess($row->text);
                $tokens = $prep['stemmed_tokens'];
            }

            foreach ($tokens as $token) {
                $token = trim($token);
                if (empty($token)) continue;

                $this->vocabulary[$token] = true;
                $this->classWordCounts[$label]++;

                if (!isset($this->wordFreqPerClass[$label][$token])) {
                    $this->wordFreqPerClass[$label][$token] = 0;
                }
                $this->wordFreqPerClass[$label][$token]++;
            }
        }

        $this->isTrained = true;
    }

    /**
     * Hitung TF-IDF untuk dokumen teks terhadap vocabulary training
     */
    public function calculateTfIdf(array $documents): array
    {
        $docCount = count($documents);
        if ($docCount === 0) return [];

        // 1. Term Frequency (TF) per dokumen
        $docTfs = [];
        $docFrequencies = [];

        foreach ($documents as $docId => $tokens) {
            $termCounts = array_count_values($tokens);
            $totalTerms = count($tokens);
            $docTfs[$docId] = [];

            foreach ($termCounts as $term => $cnt) {
                $docTfs[$docId][$term] = $totalTerms > 0 ? ($cnt / $totalTerms) : 0;
                if (!isset($docFrequencies[$term])) {
                    $docFrequencies[$term] = 0;
                }
                $docFrequencies[$term]++;
            }
        }

        // 2. IDF dan TF-IDF
        $tfidfResults = [];
        foreach ($docTfs as $docId => $tfs) {
            $tfidfResults[$docId] = [];
            foreach ($tfs as $term => $tf) {
                $df = $docFrequencies[$term] ?? 1;
                $idf = log(($docCount + 1) / ($df + 1)) + 1; // Smooth IDF
                $tfidfResults[$docId][$term] = round($tf * $idf, 4);
            }
            arsort($tfidfResults[$docId]);
        }

        return [
            'doc_frequencies' => $docFrequencies,
            'tfidf' => $tfidfResults,
        ];
    }

    /**
     * Prediksi sentimen untuk teks esai dengan Multinomial Naive Bayes + Laplace Smoothing
     */
    public function predict(string $text, ?int $aspectId = null): array
    {
        if (!$this->isTrained) {
            $this->train($aspectId);
        }

        $prep = $this->nlp->preprocess($text);
        $tokens = $prep['stemmed_tokens'];

        if (empty($tokens)) {
            return [
                'sentiment' => 'Netral',
                'confidence' => 0.3333,
                'scores' => ['Positif' => 0.3333, 'Negatif' => 0.3333, 'Netral' => 0.3334],
                'preprocessing' => $prep,
            ];
        }

        $vocabSize = max(1, count($this->vocabulary));
        $logPosteriors = [];

        foreach ($this->classes as $cls) {
            // Prior probability: P(C) dengan smoothing
            $prior = ($this->classDocCounts[$cls] + 1) / ($this->totalDocs + count($this->classes));
            $logProb = log($prior);

            $totalWordsInClass = $this->classWordCounts[$cls];

            // Likelihood: P(w|C) dengan Laplace smoothing
            foreach ($tokens as $token) {
                $countInCls = $this->wordFreqPerClass[$cls][$token] ?? 0;
                $wordProb = ($countInCls + 1) / ($totalWordsInClass + $vocabSize);
                $logProb += log($wordProb);
            }

            $logPosteriors[$cls] = $logProb;
        }

        // Normalisasi log probabilities ke persentase (Softmax trick)
        $maxLog = max($logPosteriors);
        $expScores = [];
        $sumExp = 0;
        foreach ($logPosteriors as $cls => $lp) {
            $expScores[$cls] = exp($lp - $maxLog);
            $sumExp += $expScores[$cls];
        }

        $normalizedScores = [];
        foreach ($expScores as $cls => $val) {
            $normalizedScores[$cls] = round($sumExp > 0 ? ($val / $sumExp) : 0, 4);
        }

        arsort($normalizedScores);
        $predictedClass = array_key_first($normalizedScores);
        $confidence = $normalizedScores[$predictedClass];

        return [
            'sentiment' => $predictedClass,
            'confidence' => $confidence,
            'scores' => $normalizedScores,
            'preprocessing' => $prep,
        ];
    }

    /**
     * Hitung metrik evaluasi model (Accuracy, Precision, Recall, F1-Score)
     * Menggunakan data latih atau k-fold / test set.
     */
    public function evaluateModel(): array
    {
        $datasets = TrainingDataset::all();
        if ($datasets->count() === 0) {
            return [
                'total_samples' => 0,
                'accuracy' => 0,
                'precision' => 0,
                'recall' => 0,
                'f1_score' => 0,
                'confusion_matrix' => [],
            ];
        }

        // Confusion matrix: [actual][predicted]
        $cm = [];
        foreach ($this->classes as $act) {
            foreach ($this->classes as $pred) {
                $cm[$act][$pred] = 0;
            }
        }

        $correct = 0;
        $total = $datasets->count();

        foreach ($datasets as $item) {
            $pred = $this->predict($item->text, $item->aspect_id);
            $predLabel = $pred['sentiment'];
            $actualLabel = $item->label;

            if (isset($cm[$actualLabel][$predLabel])) {
                $cm[$actualLabel][$predLabel]++;
            }

            if ($predLabel === $actualLabel) {
                $correct++;
            }
        }

        $accuracy = $total > 0 ? round(($correct / $total) * 100, 2) : 0;

        // Macro-averaged Precision, Recall, F1
        $precisions = [];
        $recalls = [];
        $f1s = [];

        foreach ($this->classes as $c) {
            $tp = $cm[$c][$c] ?? 0;
            $fp = 0;
            $fn = 0;

            foreach ($this->classes as $other) {
                if ($other !== $c) {
                    $fp += $cm[$other][$c] ?? 0;
                    $fn += $cm[$c][$other] ?? 0;
                }
            }

            $prec = ($tp + $fp) > 0 ? ($tp / ($tp + $fp)) : 0;
            $rec = ($tp + $fn) > 0 ? ($tp / ($tp + $fn)) : 0;
            $f1 = ($prec + $rec) > 0 ? (2 * $prec * $rec / ($prec + $rec)) : 0;

            $precisions[$c] = $prec;
            $recalls[$c] = $rec;
            $f1s[$c] = $f1;
        }

        $macroPrecision = round((array_sum($precisions) / count($this->classes)) * 100, 2);
        $macroRecall = round((array_sum($recalls) / count($this->classes)) * 100, 2);
        $macroF1 = round((array_sum($f1s) / count($this->classes)) * 100, 2);

        return [
            'total_samples' => $total,
            'accuracy' => $accuracy,
            'precision' => $macroPrecision,
            'recall' => $macroRecall,
            'f1_score' => $macroF1,
            'confusion_matrix' => $cm,
            'per_class' => [
                'precision' => $precisions,
                'recall' => $recalls,
                'f1' => $f1s,
            ]
        ];
    }
}
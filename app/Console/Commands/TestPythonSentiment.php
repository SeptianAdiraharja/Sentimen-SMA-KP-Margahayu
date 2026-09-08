<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestPythonSentiment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mbg:test-python';
    protected $description = 'Test Python Sentiment Service with .pkl model';

    public function handle(\App\Services\PythonSentimentService $svc)
    {
        $this->info("=== UJI SINGLE PREDICT VIA PYTHON & .PKL ===");
        $res = $svc->predict("Makanannya sangat lezat dan bumbunya gurih pas");
        $this->line("Raw        : " . ($res['raw'] ?? ''));
        $this->line("Clean      : " . ($res['preprocessing']['clean'] ?? ''));
        $this->line("Stemmed    : " . ($res['preprocessing']['stemmed_text'] ?? ''));
        $this->line("Sentimen   : " . ($res['sentiment'] ?? ''));
        $this->line("Confidence : " . ($res['confidence'] ?? ''));
        $this->line("Scores     : " . json_encode($res['scores'] ?? []));

        $this->info("\n=== UJI BATCH PREDICT VIA PYTHON ===");
        $batch = [
            ['id' => 1, 'text' => 'Porsi terlalu sedikit lapar lagi tidak kenyang'],
            ['id' => 2, 'text' => 'Menu sangat variatif berganti setiap hari kreatif'],
            ['id' => 3, 'text' => 'Biasa saja standar kantin'],
        ];
        $batchRes = $svc->predictBatch($batch);
        foreach ($batchRes as $b) {
            $this->line("[ID: {$b['id']}] {$b['raw']} -> Sentimen: {$b['sentiment']} (Confidence: {$b['confidence']})");
        }
    }
}

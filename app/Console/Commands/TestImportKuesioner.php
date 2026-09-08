<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestImportKuesioner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mbg:check-import {--reimport : Hapus respon lama dan import ulang dari Excel}';
    protected $description = 'Periksa jumlah respon per aspek dan jalankan import ulang jika diperlukan';

    public function handle()
    {
        $this->info("=== STATUS ASPEK & RESPON JAWABAN ===");
        $aspects = \App\Models\Aspect::withCount('responseAnswers')->orderBy('sort_order')->get();
        foreach ($aspects as $asp) {
            $this->line("Aspek [{$asp->sort_order}] {$asp->name} ({$asp->code}): {$asp->response_answers_count} respon");
        }

        $this->info("\n=== STATUS PERTANYAAN DI DATABASE ===");
        $questions = \App\Models\Question::with('aspect')->orderBy('question_number')->get();
        foreach ($questions as $q) {
            $aspName = $q->aspect ? $q->aspect->name : '(NULL/RATING)';
            $this->line("No. {$q->question_number} [{$q->type}] Aspek: {$aspName} | Teks: " . substr($q->question_text, 0, 50));
        }

        if ($this->option('reimport')) {
            $this->warn("\nMemulai proses import ulang dari data kuesioner.xlsx...");
            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
            \App\Models\ResponseAnswer::truncate();
            \App\Models\QuestionnaireResponse::truncate();
            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

            $controller = app(\App\Http\Controllers\Admin\ResponseController::class);
            $request = new \Illuminate\Http\Request();
            $period = \App\Models\Period::where('is_active', true)->first() ?? \App\Models\Period::first();
            $request->merge(['period_id' => $period->id]);

            $controller->import($request, app(\App\Services\NaiveBayesService::class));
            $this->info("Import selesai!");

            $this->info("\n=== STATUS ASPEK SETELAH RE-IMPORT ===");
            $aspectsAfter = \App\Models\Aspect::withCount('responseAnswers')->orderBy('sort_order')->get();
            foreach ($aspectsAfter as $asp) {
                $this->line("Aspek [{$asp->sort_order}] {$asp->name} ({$asp->code}): {$asp->response_answers_count} respon");
            }
        }
    }
}

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PeriodController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\ResponseController;
use App\Http\Controllers\Admin\AnalysisController;
use App\Http\Controllers\Admin\ReportController;

/*
|--------------------------------------------------------------------------
| 1. Siswa (Tanpa Login) - Form Kuesioner Anonim MBG
|--------------------------------------------------------------------------
*/
Route::get('/', [QuestionnaireController::class, 'index'])->name('questionnaire.index');
Route::post('/kuesioner', [QuestionnaireController::class, 'store'])->name('questionnaire.store');
Route::get('/kuesioner/terima-kasih', [QuestionnaireController::class, 'success'])->name('questionnaire.success');

/*
|--------------------------------------------------------------------------
| Autentikasi Admin
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 2. Admin / Pihak Sekolah (Wajib Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Visual
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Periode Kuesioner
    Route::get('/periods', [PeriodController::class, 'index'])->name('periods.index');
    Route::post('/periods', [PeriodController::class, 'store'])->name('periods.store');
    Route::put('/periods/{period}', [PeriodController::class, 'update'])->name('periods.update');
    Route::patch('/periods/{period}/toggle', [PeriodController::class, 'toggleStatus'])->name('periods.toggle');
    Route::delete('/periods/{period}', [PeriodController::class, 'destroy'])->name('periods.destroy');

    // Pertanyaan Kuesioner
    Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');
    Route::post('/questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::put('/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');

    // Seluruh Data Jawaban Kuesioner
    Route::get('/responses', [ResponseController::class, 'index'])->name('responses.index');
    Route::get('/responses/{response}', [ResponseController::class, 'show'])->name('responses.show');
    Route::delete('/responses/{response}', [ResponseController::class, 'destroy'])->name('responses.destroy');
    Route::post('/responses/import', [ResponseController::class, 'import'])->name('responses.import');

    // Proses & Hasil Analisis Sentimen (NLP, TF-IDF, Naive Bayes)
    Route::get('/analysis', [AnalysisController::class, 'index'])->name('analysis.index');
    Route::post('/analysis/run', [AnalysisController::class, 'runAnalysis'])->name('analysis.run');
    Route::get('/analysis/tfidf', [AnalysisController::class, 'tfidf'])->name('analysis.tfidf');
    Route::get('/analysis/metrics', [AnalysisController::class, 'modelMetrics'])->name('analysis.metrics');
    Route::post('/analysis/dataset', [AnalysisController::class, 'storeDataset'])->name('analysis.dataset.store');
    Route::delete('/analysis/dataset/{dataset}', [AnalysisController::class, 'destroyDataset'])->name('analysis.dataset.destroy');

    // Laporan Hasil Evaluasi (Visual, PDF/Print, Ekspor Excel)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/print', [ReportController::class, 'print'])->name('reports.print');
    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');
});

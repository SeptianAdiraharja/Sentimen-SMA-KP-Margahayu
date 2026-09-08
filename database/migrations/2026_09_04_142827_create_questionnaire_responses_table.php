<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('questionnaire_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('periods')->onDelete('cascade');
            $table->string('respondent_code')->nullable(); // Siswa anonim (e.g. RESP-001)
            $table->string('overall_rating')->nullable(); // Sangat Puas, Cukup Puas, Kurang Puas, Tidak Puas, Sangat Tidak Puas
            $table->integer('overall_rating_score')->nullable(); // 1 - 5
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questionnaire_responses');
    }
};

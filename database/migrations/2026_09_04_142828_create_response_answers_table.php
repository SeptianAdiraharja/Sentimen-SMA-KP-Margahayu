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
        Schema::create('response_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('response_id')->constrained('questionnaire_responses')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->foreignId('aspect_id')->nullable()->constrained('aspects')->onDelete('set null');
            $table->text('raw_answer')->nullable();
            
            // Tahapan NLP Preprocessing
            $table->text('clean_answer')->nullable(); // Case folding & regex cleaning
            $table->text('tokens')->nullable(); // JSON tokens array
            $table->text('filtered_tokens')->nullable(); // Stopword removed
            $table->text('stemmed_text')->nullable(); // Hasil stemming Sastrawi
            
            // Klasifikasi Sentimen Naive Bayes
            $table->enum('sentiment', ['Positif', 'Negatif', 'Netral'])->nullable();
            $table->decimal('confidence_score', 8, 4)->nullable(); // Probabilitas / skor Naive Bayes
            $table->json('sentiment_details')->nullable(); // Log probabilitas per kelas
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('response_answers');
    }
};

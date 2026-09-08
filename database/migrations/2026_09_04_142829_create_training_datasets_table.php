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
        Schema::create('training_datasets', function (Blueprint $table) {
            $table->id();
            $table->text('text');
            $table->enum('label', ['Positif', 'Negatif', 'Netral']);
            $table->foreignId('aspect_id')->nullable()->constrained('aspects')->onDelete('set null');
            $table->text('stemmed_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_datasets');
    }
};

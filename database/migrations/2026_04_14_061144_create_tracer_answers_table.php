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
    Schema::create('tracer_answers', function (Blueprint $table) {
        $table->id(); // TA_ID
        // Foreign Key to the user's specific form submission
        $table->foreignId('tracer_response_id')->constrained('tracer_responses')->cascadeOnDelete();
        // Foreign Key to the specific question being answered
        $table->foreignId('tq_id')->constrained('tracer_questions')->cascadeOnDelete();
        
        $table->text('answer_value'); // Text to accommodate long strings, numbers, or JSON arrays
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracer_answers');
    }
};

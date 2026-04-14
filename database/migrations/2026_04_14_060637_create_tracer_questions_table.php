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
    Schema::create('tracer_questions', function (Blueprint $table) {
        $table->id(); // TQ_ID
        $table->foreignId('form_id')->constrained('tracer_forms')->cascadeOnDelete();
        $table->string('type'); // e.g., 'text', 'radio', 'checkbox', 'dropdown'
        $table->text('question_text');
        $table->text('description')->nullable();
        $table->boolean('is_required')->default(true);
        $table->integer('order_priority')->default(0);
        $table->json('settings')->nullable(); // Stores answer choices dynamically
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracer_questions');
    }
};

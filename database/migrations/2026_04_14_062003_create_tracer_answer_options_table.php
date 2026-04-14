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
    Schema::create('tracer_answer_options', function (Blueprint $table) {
        $table->id(); // TAO_ID
        $table->foreignId('tq_id')->constrained('tracer_questions')->cascadeOnDelete();
        $table->string('option_label');
        $table->string('option_value');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracer_answer_options');
    }
};

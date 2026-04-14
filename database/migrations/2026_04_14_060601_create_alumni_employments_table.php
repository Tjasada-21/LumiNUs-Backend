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
    Schema::create('alumni_employments', function (Blueprint $table) {
        $table->id(); // Employment_ID
        $table->foreignId('alumni_id')->constrained('alumnis')->cascadeOnDelete();
        $table->string('job_title');
        $table->string('company');
        $table->string('location');
        $table->text('career_description')->nullable();
        $table->date('start_date');
        $table->date('end_date')->nullable();
        $table->boolean('is_current')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni_employments');
    }
};

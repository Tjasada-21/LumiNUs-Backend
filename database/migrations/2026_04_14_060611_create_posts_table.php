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
    Schema::create('posts', function (Blueprint $table) {
        $table->id(); // Acts as Post_ID
        // Foreign Key to Alumni
        $table->foreignId('alumni_id')->constrained('alumnis')->cascadeOnDelete();
        
        $table->longText('caption')->nullable();
        $table->string('moderation_status')->default('approved'); // e.g., approved, flagged, removed
        $table->timestamps(); // Handles CreatedAt
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};

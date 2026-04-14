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
    Schema::create('reactions', function (Blueprint $table) {
        $table->id(); // Reaction_ID
        $table->foreignId('alumni_id')->constrained('alumnis')->cascadeOnDelete();
        $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
        $table->enum('reaction', ['like', 'love', 'insightful', 'support']); 
        $table->timestamps(); // Handles CreatedAt
        
        // Ensure one reaction per user per post
        $table->unique(['alumni_id', 'post_id']); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};

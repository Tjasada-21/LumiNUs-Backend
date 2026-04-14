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
    Schema::create('followers', function (Blueprint $table) {
        $table->id(); // Acts as Follow_ID
        
        // The person who is following
        $table->foreignId('follower_alumni_id')->constrained('alumnis')->cascadeOnDelete();
        // The person being followed
        $table->foreignId('followed_alumni_id')->constrained('alumnis')->cascadeOnDelete();
        
        // Prevent duplicate follows
        $table->unique(['follower_alumni_id', 'followed_alumni_id']);
        
        $table->timestamps(); // Handles CreatedAt
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followers');
    }
};

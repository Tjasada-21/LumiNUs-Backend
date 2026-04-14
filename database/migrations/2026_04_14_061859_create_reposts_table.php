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
    Schema::create('reposts', function (Blueprint $table) {
        $table->id(); // Repost_ID
        $table->foreignId('alumni_id')->constrained('alumnis')->cascadeOnDelete();
        $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
        $table->longText('caption')->nullable();
        $table->string('moderation_status')->default('approved');
        $table->timestamps(); // Handles CreatedAt
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reposts');
    }
};

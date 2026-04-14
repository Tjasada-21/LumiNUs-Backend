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
    Schema::create('messages', function (Blueprint $table) {
        $table->id(); // Messages_ID
        $table->foreignId('sender_id')->constrained('alumnis')->cascadeOnDelete();
        $table->foreignId('receiver_id')->constrained('alumnis')->cascadeOnDelete();
        $table->longText('content');
        $table->boolean('is_read')->default(false);
        $table->timestamps(); // Handles Timestamp
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};

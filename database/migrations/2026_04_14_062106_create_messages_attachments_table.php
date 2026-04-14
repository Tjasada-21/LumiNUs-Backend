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
    Schema::create('messages_attachments', function (Blueprint $table) {
        $table->id(); // Messages_Attachment_ID
        $table->foreignId('message_id')->constrained('messages')->cascadeOnDelete();
        $table->enum('attachment_type', ['image', 'document', 'video']);
        $table->string('attachment_path');
        $table->timestamps(); // Handles CreatedAt
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages_attachments');
    }
};

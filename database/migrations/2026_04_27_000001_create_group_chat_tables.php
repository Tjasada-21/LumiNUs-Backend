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
        Schema::create('group_chats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('avatar_url')->nullable();
            $table->foreignId('created_by')->constrained('alumnis')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('group_chat_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_chat_id')->constrained('group_chats')->cascadeOnDelete();
            $table->foreignId('alumni_id')->constrained('alumnis')->cascadeOnDelete();
            $table->unsignedBigInteger('last_read_message_id')->nullable();
            $table->timestamps();

            $table->unique(['group_chat_id', 'alumni_id']);
        });

        Schema::create('group_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_chat_id')->constrained('group_chats')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('alumnis')->cascadeOnDelete();
            $table->longText('content');
            $table->json('reactions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_messages');
        Schema::dropIfExists('group_chat_members');
        Schema::dropIfExists('group_chats');
    }
};
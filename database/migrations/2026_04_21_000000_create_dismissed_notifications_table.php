<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dismissed_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alumni_id');
            $table->string('notification_key');
            $table->timestamps();

            $table->unique(['alumni_id', 'notification_key']);
            $table->foreign('alumni_id')->references('id')->on('alumnis')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dismissed_notifications');
    }
};
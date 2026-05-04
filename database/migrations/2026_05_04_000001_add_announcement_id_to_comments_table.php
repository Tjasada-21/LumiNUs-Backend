<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // Make post_id nullable so comments can belong to announcements instead
            $table->foreignId('post_id')->nullable()->change();
            // Add nullable announcement_id FK
            $table->foreignId('announcement_id')->nullable()->constrained('announcements')->cascadeOnDelete()->after('post_id');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['announcement_id']);
            $table->dropColumn('announcement_id');
            $table->foreignId('post_id')->nullable(false)->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reactions', function (Blueprint $table) {
            // Drop old unique constraint so we can replace it
            $table->dropUnique(['alumni_id', 'post_id']);
            // Make post_id nullable so reactions can belong to announcements instead
            $table->foreignId('post_id')->nullable()->change();
            // Add nullable announcement_id FK
            $table->foreignId('announcement_id')->nullable()->constrained('announcements')->cascadeOnDelete()->after('post_id');
            // New unique constraint covering both cases
            $table->unique(['alumni_id', 'post_id', 'announcement_id']);
        });
    }

    public function down(): void
    {
        Schema::table('reactions', function (Blueprint $table) {
            $table->dropUnique(['alumni_id', 'post_id', 'announcement_id']);
            $table->dropForeign(['announcement_id']);
            $table->dropColumn('announcement_id');
            $table->foreignId('post_id')->nullable(false)->change();
            $table->unique(['alumni_id', 'post_id']);
        });
    }
};

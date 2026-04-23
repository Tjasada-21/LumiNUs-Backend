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
    Schema::table('announcements', function (Blueprint $table) {
        // Adds the new column, placing it logically after 'status'
        $table->timestamp('scheduled_post_at')->nullable()->after('status');
    });
}

public function down(): void
{
    Schema::table('announcements', function (Blueprint $table) {
        // Safe rollback instruction
        $table->dropColumn('scheduled_post_at');
    });
}
};

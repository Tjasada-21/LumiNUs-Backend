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
        // 1. Rename the title column to match the ERD
        $table->renameColumn('announcement_title', 'title');
        
        // 2. Add the new status integer column
        // We set a default of 1 (e.g., Active) so it doesn't break existing rows
        $table->integer('status')->default(1)->after('date_posted'); 
    });
}

public function down(): void
{
    Schema::table('announcements', function (Blueprint $table) {
        // Safe rollback instructions
        $table->dropColumn('status');
        $table->renameColumn('title', 'announcement_title');
    });
}
};

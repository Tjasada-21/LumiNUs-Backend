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
    Schema::table('events', function (Blueprint $table) {
        // 1. Drop the old varchar column
        $table->dropColumn('status');
    });

    Schema::table('events', function (Blueprint $table) {
        // 2. Add the new integer column back in the exact same spot
        // Adding a default of 1 (e.g., 'Upcoming' or 'Active') is good practice for status ints!
        $table->integer('status')->default(1)->after('max_capacity');
    });
}

public function down(): void
{
    Schema::table('events', function (Blueprint $table) {
        // Rollback instructions
        $table->dropColumn('status');
    });

    Schema::table('events', function (Blueprint $table) {
        $table->string('status')->nullable()->after('max_capacity');
    });
}
};

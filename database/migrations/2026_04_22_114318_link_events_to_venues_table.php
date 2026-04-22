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
        // This creates the official relationship between the two tables
        $table->foreign('venue_id')->references('id')->on('venues')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('events', function (Blueprint $table) {
        // Safe rollback: this drops the relationship line, but keeps the column
        $table->dropForeign(['venue_id']);
    });
}
};

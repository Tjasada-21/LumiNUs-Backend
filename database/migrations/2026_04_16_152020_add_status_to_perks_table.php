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
    Schema::table('perks', function (Blueprint $table) {
        // Adds the status column right after valid_until to match your ERD visually
        $table->string('status')->default('active')->after('valid_until'); 
    });
}

public function down(): void
{
    Schema::table('perks', function (Blueprint $table) {
        // Reverts the change if you ever need to rollback
        $table->dropColumn('status');
    });
}
};

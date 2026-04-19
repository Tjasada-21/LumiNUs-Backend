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
    Schema::table('alumnis', function (Blueprint $table) {
        $table->string('program')->nullable()->after('year_graduated');
        $table->string('card_photo')->nullable()->after('password_hash');
    });
}

public function down(): void
{
    Schema::table('alumnis', function (Blueprint $table) {
        $table->dropColumn(['program', 'card_photo']);
    });
}
};

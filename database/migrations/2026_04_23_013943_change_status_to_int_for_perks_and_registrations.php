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
    // 1. Drop the old varchar columns from both tables
    Schema::table('perks', function (Blueprint $table) {
        $table->dropColumn('status');
    });

    Schema::table('event_registrations', function (Blueprint $table) {
        $table->dropColumn('status');
    });

    // 2. Add the new integer columns back
    Schema::table('perks', function (Blueprint $table) {
        // Adding it back with a default of 1
        $table->integer('status')->default(1);
    });

    Schema::table('event_registrations', function (Blueprint $table) {
        // Placing it perfectly after the boolean confirmation
        $table->integer('status')->default(1)->after('registration_confirmation');
    });
}

public function down(): void
{
    // Safe rollback instructions
    Schema::table('perks', function (Blueprint $table) {
        $table->dropColumn('status');
    });

    Schema::table('event_registrations', function (Blueprint $table) {
        $table->dropColumn('status');
    });

    Schema::table('perks', function (Blueprint $table) {
        $table->string('status')->nullable();
    });

    Schema::table('event_registrations', function (Blueprint $table) {
        $table->string('status')->nullable()->after('registration_confirmation');
    });
}
};

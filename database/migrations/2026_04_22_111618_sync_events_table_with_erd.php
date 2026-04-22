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
        // 1. Remove the old location column
        $table->dropColumn('location');

        // 2. Add the new structural columns
        $table->string('status')->nullable()->after('max_capacity');
        $table->string('event_type')->nullable()->after('status');
        $table->string('platform')->nullable()->after('event_type');
        $table->string('platform_url')->nullable()->after('platform');
        
        // 3. Add the Foreign Key for Venue (Placed after admin_id)
        // Using unsignedBigInteger is safest here in case your 'venues' table doesn't exist yet
        $table->unsignedBigInteger('venue_id')->nullable()->after('admin_id'); 
    });
}

public function down(): void
{
    Schema::table('events', function (Blueprint $table) {
        // Safe rollback instructions
        $table->string('location')->nullable();
        $table->dropColumn(['status', 'event_type', 'platform', 'platform_url', 'venue_id']);
    });
}
};

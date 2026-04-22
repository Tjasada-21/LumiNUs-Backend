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
    Schema::table('venues', function (Blueprint $table) {
        $table->string('name')->after('id');
        $table->string('address')->nullable()->after('name');
        $table->decimal('latitude', 10, 8)->nullable()->after('address');
        $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
    });
}

public function down(): void
{
    Schema::table('venues', function (Blueprint $table) {
        // Safe rollback if needed
        $table->dropColumn(['name', 'address', 'latitude', 'longitude']);
    });
}
};

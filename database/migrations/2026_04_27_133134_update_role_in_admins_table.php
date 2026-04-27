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
        // First, drop the old generic role column
        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'role')) {
                $table->dropColumn('role');
            }
        });

        // Then, rebuild it with your exact required roles
        Schema::table('admins', function (Blueprint $table) {
            $table->enum('role', [
                'Executive Director',
                'Academic Director',
                'Coordinator',
                'Assistant Coordinator'
            ])->default('Coordinator'); // Sets a safe default just in case
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->string('role')->default('admin');
        });
    }
};

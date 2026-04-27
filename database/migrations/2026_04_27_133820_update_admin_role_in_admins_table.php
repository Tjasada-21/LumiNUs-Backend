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
        Schema::table('admins', function (Blueprint $table) {
            // 1. Remove the incorrect 'role' column if it exists
            if (Schema::hasColumn('admins', 'role')) {
                $table->dropColumn('role');
            }
            
            // 2. Drop the old 'admin_role' column so we can rebuild it cleanly
            if (Schema::hasColumn('admins', 'admin_role')) {
                $table->dropColumn('admin_role');
            }
        });

        // 3. Recreate 'admin_role' with your exact requested roles
        Schema::table('admins', function (Blueprint $table) {
            $table->enum('admin_role', [
                'Executive Director',
                'Academic Director',
                'Coordinator',
                'Assistant Coordinator'
            ])->default('Coordinator');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'admin_role')) {
                $table->dropColumn('admin_role');
            }
            // Fallback to a standard string if you ever rollback
            $table->string('admin_role')->default('admin'); 
        });
    }
};

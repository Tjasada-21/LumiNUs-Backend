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
    Schema::create('admins', function (Blueprint $table) {
        $table->id(); // Acts as Admin_ID
        $table->string('admin_first_name');
        $table->string('admin_middle_name')->nullable();
        $table->string('admin_last_name');
        $table->string('admin_email')->unique();
        $table->string('admin_password_hash');
        $table->enum('admin_role', ['super_admin', 'moderator', 'event_coordinator']);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};

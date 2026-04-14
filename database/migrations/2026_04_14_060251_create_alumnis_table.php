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
    Schema::create('alumnis', function (Blueprint $table) {
        $table->id(); // Acts as Alumni_ID (INT)
        $table->string('first_name');
        $table->string('middle_name')->nullable();
        $table->string('last_name');
        $table->date('date_of_birth');
        $table->string('sex');
        $table->date('year_graduated');
        $table->string('alumni_photo')->nullable();
        $table->text('alumni_bio')->nullable();
        $table->string('student_id_number')->unique();
        $table->string('email')->unique();
        $table->string('phone_number')->nullable();
        $table->string('password_hash');
        $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnis');
    }
};

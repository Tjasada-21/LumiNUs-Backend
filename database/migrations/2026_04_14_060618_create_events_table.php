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
    Schema::create('events', function (Blueprint $table) {
        $table->id(); // Acts as Events_ID
        // Foreign Key to Admin
        $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
        
        $table->string('title');
        $table->string('description');
        $table->date('start_date');
        $table->date('end_date');
        $table->string('location');
        $table->integer('max_capacity');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

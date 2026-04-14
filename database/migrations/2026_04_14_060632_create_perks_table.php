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
    Schema::create('perks', function (Blueprint $table) {
        $table->id(); // Perk_ID
        $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
        $table->string('title');
        $table->string('description');
        $table->date('valid_until');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perks');
    }
};

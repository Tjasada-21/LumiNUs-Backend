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
    Schema::create('addresses', function (Blueprint $table) {
        $table->id(); // Address_ID
        $table->foreignId('alumni_id')->constrained('alumnis')->cascadeOnDelete();
        $table->string('address_type'); // e.g., 'Current', 'Permanent'
        $table->string('region');
        $table->string('province');
        $table->string('municipality');
        $table->string('barangay');
        $table->string('street')->nullable();
        $table->string('zip_code');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};

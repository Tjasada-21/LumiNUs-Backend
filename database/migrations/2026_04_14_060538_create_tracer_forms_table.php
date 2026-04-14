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
    Schema::create('tracer_forms', function (Blueprint $table) {
        $table->id(); // Form_ID
        $table->string('form_title');
        $table->text('form_description')->nullable();
        $table->text('form_header')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps(); // CreatedAt & UpdatedAt
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracer_forms');
    }
};

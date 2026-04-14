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
    Schema::create('event_registrations', function (Blueprint $table) {
        $table->id(); // RSVP_ID
        $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
        $table->foreignId('alumni_id')->constrained('alumnis')->cascadeOnDelete();
        $table->date('rsvp_date');
        $table->boolean('registration_confirmation')->default(false);
        $table->string('status'); // e.g., 'Attending', 'Waitlisted'
        $table->timestamps();
        
        // Prevent an alumni from registering for the same event twice
        $table->unique(['event_id', 'alumni_id']);
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};

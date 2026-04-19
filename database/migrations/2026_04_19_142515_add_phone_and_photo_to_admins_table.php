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
        // Adding the new columns in logical order
        $table->string('phone_number')->nullable()->after('email');
        $table->string('photo')->nullable()->after('phone_number'); 
    });
}

public function down(): void
{
    Schema::table('admins', function (Blueprint $table) {
        // Safe rollback instructions
        $table->dropColumn(['phone_number', 'photo']);
    });
}
};

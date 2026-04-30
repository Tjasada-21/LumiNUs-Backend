<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('messages', function (Blueprint $table) {
        // Adding ->nullable() prevents the Postgres crash on existing rows
        $table->string('sender_type')->nullable()->after('sender_id');
        $table->string('receiver_type')->nullable()->after('receiver_id');
    });
}

public function down()
{
    Schema::table('messages', function (Blueprint $table) {
        $table->dropColumn(['sender_type', 'receiver_type']);
    });
}
};

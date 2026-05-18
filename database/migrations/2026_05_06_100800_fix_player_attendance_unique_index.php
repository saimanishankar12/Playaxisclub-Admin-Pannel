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
        Schema::table('player_attendance', function (Blueprint $table) {
        $table->dropUnique('player_attendance_player_id_unique');
        $table->unique(['player_id', 'date']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('player_attendance', function (Blueprint $table) {
        $table->dropUnique(['player_id', 'date']);
        $table->unique('player_id');
    });
    }
};

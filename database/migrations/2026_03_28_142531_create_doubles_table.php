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
        Schema::create('doubles', function (Blueprint $table) {
             $table->id();
        $table->string('doubles_pair_id', 20);    // e.g. PACD001
        $table->string('season_id', 20);           // e.g. ALK001D
        $table->unsignedBigInteger('player1_id');  // FK to players.id
        $table->unsignedBigInteger('player2_id');  // FK to players.id
        $table->timestamps();

        $table->foreign('player1_id')->references('id')->on('players')->onDelete('cascade');
        $table->foreign('player2_id')->references('id')->on('players')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doubles');
    }
};

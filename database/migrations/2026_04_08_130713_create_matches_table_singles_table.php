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
        Schema::create('matches_table_singles', function (Blueprint $table) {
            $table->id();
              $table->string('team_a');
    $table->string('team_b');
    $table->string('player_a');
    $table->string('player_b');
    $table->tinyInteger('score_a')->default(0);
    $table->tinyInteger('score_b')->default(0);
    $table->string('winner')->nullable();
    $table->string('division');
    $table->tinyInteger('court_no');
    $table->foreignId('empire_id')->nullable();
    $table->foreignId('scorer_id')->nullable();
    $table->timestamp('played_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches_table_singles');
    }
};

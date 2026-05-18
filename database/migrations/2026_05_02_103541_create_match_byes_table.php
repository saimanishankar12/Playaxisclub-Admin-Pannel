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
        Schema::create('match_byes', function (Blueprint $table) {
          $table->id();
 
            // Match classification — mirrors the same fields on match_games
            $table->string('match_type', 20);   // 'singles' | 'doubles'
            $table->string('division', 10);      // 'U-11' | 'U-13' | 'U-15' | 'U-19'
            $table->string('round', 20);         // 'quarter_final' | 'semi_final' | 'final'
 
            // The player who received the bye.
            // Singles:  players.player_id
            // Doubles:  players.season_id  (the shared team ID)
            $table->string('player_id', 100);
 
            $table->timestamps();
 
            // Index used in assignByePlayer() to find existing byes quickly
            $table->index(['match_type', 'division', 'round']);
            $table->index(['player_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_byes');
    }
};

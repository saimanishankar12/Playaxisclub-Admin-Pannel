<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();

            $table->string('court_no', 50);
            $table->string('umpire_name', 100);
            $table->string('scorer_name', 100);
            $table->enum('match_type', ['singles', 'doubles']);
            $table->enum('division', ['U-11', 'U-13', 'U-15', 'U-19']);
            $table->enum('round', ['quarter_final', 'semi_final', 'final']);

            $table->string('player1_id')->nullable();
            $table->string('player2_id')->nullable();

            $table->unsignedTinyInteger('sets_to_win')->default(1);

            $table->unsignedTinyInteger('sets_won_p1')->default(0);
            $table->unsignedTinyInteger('sets_won_p2')->default(0);

            $table->unsignedTinyInteger('current_set')->default(1);
            $table->unsignedTinyInteger('score_p1')->default(0);
            $table->unsignedTinyInteger('score_p2')->default(0);

            $table->string('winner_id')->nullable();

            $table->enum('status', ['setup', 'live', 'completed'])->default('setup');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
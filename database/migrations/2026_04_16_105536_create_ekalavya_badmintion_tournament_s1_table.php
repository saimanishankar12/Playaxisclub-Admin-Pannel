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
        Schema::create('ekalavya_badmintion_tournament_s1', function (Blueprint $table) {
           $table->id();

            // e.g. PAC00001
          $table->string('player_id');

            // e.g. ALK0001S (singles) or ALK0001D (doubles)
            $table->string('season_id');

            $table->string('match_type')->default('single'); // single | double
            $table->string('age_category')->nullable();      // U11 | U13 | U15 | U19

            $table->unsignedInteger('total_matches')->default(0);
            $table->unsignedInteger('won')->default(0);
            $table->unsignedInteger('lost')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekalavya_badmintion_tournament_s1');
    }
};

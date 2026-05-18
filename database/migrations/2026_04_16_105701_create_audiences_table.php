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
        Schema::create('audiences', function (Blueprint $table) {
            $table->id();

            // Auto-generated e.g. PAC00001AUD
            $table->string('audience_id')->unique();

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 15);
            $table->string('city');
            $table->unsignedTinyInteger('age');

            // Which tournament season they attended
            $table->unsignedBigInteger('tournament_season_id')->nullable();
            $table->foreign('tournament_season_id')
                  ->references('id')->on('tournament_seasons')
                  ->onDelete('set null');

            // Lucky draw
            $table->boolean('is_winner')->default(false);
            $table->unsignedTinyInteger('won_day')->nullable(); // Day 1, 2, 3

            $table->timestamps();   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audiences');
    }
};

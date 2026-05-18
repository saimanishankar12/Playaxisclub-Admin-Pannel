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
        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();

            $table->string('name');           // e.g. Brillen

            // Package options: 25000 / 50000 / 75000 / 100000
            $table->enum('package', ['25000', '50000', '75000', '100000']);

            // Linked to tournaments table
            $table->unsignedBigInteger('tournament_id');
            $table->foreign('tournament_id')
                  ->references('id')->on('tournaments')
                  ->onDelete('cascade');

            // Linked to tournament_seasons table (season number)
            $table->unsignedBigInteger('tournament_season_id');
            $table->foreign('tournament_season_id')
                  ->references('id')->on('tournament_seasons')
                  ->onDelete('cascade');

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsors');
    }
};

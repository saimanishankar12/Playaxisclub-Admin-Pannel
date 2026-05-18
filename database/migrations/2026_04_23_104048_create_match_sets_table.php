<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_sets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('match_id')
                  ->constrained('matches')
                  ->onDelete('cascade');

            $table->unsignedTinyInteger('set_number');
            $table->unsignedTinyInteger('score_p1');
            $table->unsignedTinyInteger('score_p2');
            $table->enum('winner', ['p1', 'p2']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_sets');
    }
};
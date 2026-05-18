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
        Schema::create('active_matches', function (Blueprint $table) {    
        $table->id();
        $table->unsignedBigInteger('admin_id')->unique(); // one live match per admin
        $table->json('match_state');                      // full state stored here
        $table->timestamps();
        $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('active_matches');
    }
};

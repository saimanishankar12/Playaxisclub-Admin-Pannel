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
        Schema::create('player_attendance', function (Blueprint $table) {
             $table->id();
    $table->string('player_id')->charset('utf8mb4')->collation('utf8mb4_general_ci');
    $table->boolean('is_present')->default(false);
    $table->timestamp('marked_at')->nullable();
    $table->unsignedBigInteger('marked_by')->nullable();
    $table->timestamps();

    $table->unique('player_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_attendance');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->enum('group_type', ['knockout', 'round_robin'])->default('knockout')->after('round');
            $table->unsignedTinyInteger('match_group')->default(0)->after('group_type'); // for round robin grouping
            $table->string('declared_winner_id')->nullable()->after('winner_id');        // admin declared winner
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn(['group_type', 'match_group', 'declared_winner_id']);
        });
    }
};
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('players', function (Blueprint $table) {
            // add address after state_id
            $table->text('address')->nullable()->after('state_id');

            // make city nullable
           $table->integer('city_id')->unsigned()->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn('address');

            // revert city back to not nullable
            $table->unsignedBigInteger('city_id')->nullable(false)->change();
        });
    }
};
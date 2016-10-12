<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSpecialDayToInSaintsDays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_saints_days', function (Blueprint $table) {
            $table->string('spec_day', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_saints_days', function (Blueprint $table) {
            $table->dropColumn('spec_day');
        });
    }
}

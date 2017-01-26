<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersAddHolidayId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('dx_users', function (Blueprint $table) {
            $table->integer('left_holiday_id')->unsigned()->nullable()->comment = 'Left reason - holiday';
            
            $table->index('left_holiday_id');            
            $table->foreign('left_holiday_id')->references('id')->on('dx_holidays');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_users', function (Blueprint $table) { 
            $table->dropForeign(['left_holiday_id']);
            $table->dropColumn(['left_holiday_id']);
        });
    }
}

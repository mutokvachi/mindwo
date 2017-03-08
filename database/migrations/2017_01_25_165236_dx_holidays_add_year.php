<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxHolidaysAddYear extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_holidays', function (Blueprint $table) {
            $table->integer('from_year')->unsigned()->nullable()->comment = 'Year (from)';
            $table->integer('to_year')->unsigned()->nullable()->comment = 'Year (to)';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_holidays', function (Blueprint $table) {            
            $table->dropColumn(['from_year']);
            $table->dropColumn(['to_year']);
        });
    }
}

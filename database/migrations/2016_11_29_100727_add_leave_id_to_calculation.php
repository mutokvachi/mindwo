<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLeaveIdToCalculation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_timeoff_calc', function (Blueprint $table) {
            $table->integer('leave_id')->unsigned()->nullable()->comment = "Leave";
            
            $table->index('leave_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_timeoff_calc', function (Blueprint $table) {            
            $table->dropIndex(['leave_id']);
            $table->dropColumn(['leave_id']);
        });
    }
}

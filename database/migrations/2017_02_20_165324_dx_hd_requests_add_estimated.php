<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxHdRequestsAddEstimated extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_hd_requests', function (Blueprint $table) {
            $table->datetime('planned_finish')->nullable()->comment = 'Plānotais izpildes laiks';            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_hd_requests', function (Blueprint $table) {           
            $table->dropColumn(['planned_finish']);
        });
    }
}

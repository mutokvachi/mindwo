<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxHdRequestsAddData1Id extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_hd_requests', function (Blueprint $table) {
            $table->integer('data1_id')->nullable();
            
            $table->index('data1_id');            
            $table->foreign('data1_id')->references('id')->on('dx_data');
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
            $table->dropForeign(['data1_id']);
            $table->dropColumn(['data1_id']);
        });
    }
}

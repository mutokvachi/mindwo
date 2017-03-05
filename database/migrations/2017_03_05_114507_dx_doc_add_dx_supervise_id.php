<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxDocAddDxSuperviseId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_doc', function (Blueprint $table) {
            $table->integer('dx_supervise_id')->unsigned()->nullable()->comment = 'Supervision domain';   
            
            $table->index('dx_supervise_id');            
            $table->foreign('dx_supervise_id')->references('id')->on('dx_supervise');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_doc', function (Blueprint $table) {           
            $table->dropForeign(['dx_supervise_id']);
            $table->dropColumn(['dx_supervise_id']);
        });
    }
}

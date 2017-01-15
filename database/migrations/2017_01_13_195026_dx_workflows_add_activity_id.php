<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxWorkflowsAddActivityId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_workflows', function (Blueprint $table) {
            $table->integer('activity_id')->unsigned()->nullable()->comment = trans('workflow.fld_activity'); 
            
            $table->index('activity_id');            
            $table->foreign('activity_id')->references('id')->on('dx_workflows_activities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_workflows', function (Blueprint $table) { 
            $table->dropForeign(['activity_id']);
            $table->dropColumn(['activity_id']);
        });
    }
}

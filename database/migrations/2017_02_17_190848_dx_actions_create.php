<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxActionsCreate extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_actions');
        
        Schema::create('dx_actions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 250)->comment = trans('db_dx_actions.title');
            $table->string('code', 50)->comment = trans('db_dx_actions.code');
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable(); 
        });
        
        DB::table('dx_actions')->insert(['title' => trans('db_dx_actions.start_wf'), 'code' => 'START_WORKFLOW']);
        DB::table('dx_actions')->insert(['title' => trans('db_dx_actions.start_rel_wf'), 'code' => 'START_WORKFLOW_RELATED']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_actions');
    }
}

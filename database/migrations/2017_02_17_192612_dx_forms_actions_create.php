<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxFormsActionsCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_forms_actions');
        
        Schema::create('dx_forms_actions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('form_id')->comment = trans('db_dx_forms_actions.form');
            $table->integer('action_id')->unsigned()->comment = trans('db_dx_forms_actions.action');
            $table->boolean('is_after_save')->default(0)->comment = trans('db_dx_forms_actions.is_after_save');
            
            $table->index('form_id');            
            $table->foreign('form_id')->references('id')->on('dx_forms');
            
            $table->index('action_id');            
            $table->foreign('action_id')->references('id')->on('dx_actions');
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_forms_actions');
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxMeetingsTypesCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_meetings_types');
        
        Schema::create('dx_meetings_types', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 200)->comment = trans('db_dx_meetings_types.title');
            $table->string('url', 100)->comment = trans('db_dx_meetings_types.url');
            
            $table->integer('role_prepare_id')->comment = trans('db_dx_meetings_types.role_prepare_id');
            $table->integer('role_moderator_id')->comment = trans('db_dx_meetings_types.role_moderator_id');
            $table->integer('role_decide_id')->comment = trans('db_dx_meetings_types.role_decide_id');
            
            $table->index('role_prepare_id');            
            $table->foreign('role_prepare_id')->references('id')->on('dx_roles');
            
            $table->index('role_moderator_id');            
            $table->foreign('role_moderator_id')->references('id')->on('dx_roles');
            
            $table->index('role_decide_id');            
            $table->foreign('role_decide_id')->references('id')->on('dx_roles');
            
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
        Schema::dropIfExists('dx_meetings_types');
    }
}

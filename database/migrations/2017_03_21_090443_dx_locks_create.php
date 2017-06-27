<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxLocksCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_locks');
        
        Schema::create('dx_locks', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('list_id')->comment = trans('db_dx_locks.list_id');
            $table->integer('item_id')->unsigned()->comment = trans('db_dx_locks.item_id');
            $table->integer('user_id')->comment = trans('db_dx_locks.user_id');
            $table->datetime('locked_time')->comment = trans('db_dx_locks.locked_time');
            
            $table->index('list_id');            
            $table->foreign('list_id')->references('id')->on('dx_lists');
            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_locks');
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxServerAccessCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_server_access', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->increments('id');
            $table->dateTimeTz('timestamp')->comment = trans('db_dx_server_access.timestamp');
            $table->integer('offset')->comment = trans('db_dx_server_access.offset');
            $table->string('user', 255)->comment = trans('db_dx_server_access.user');
            $table->dateTime('connect')->comment = trans('db_dx_server_access.connect');
            $table->dateTime('disconnect')->nullable()->comment = trans('db_dx_server_access.drop');
            $table->text('note')->comment = trans('db_dx_server_access.note');
            $table->integer('sshd')->comment = trans('db_dx_server_access.sshd');
            $table->string('uid', 255)->comment = trans('db_dx_server_access.uid');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dx_server_access');
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration
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
            $table->dateTimeTz('timestamp');
            $table->integer('offset');
            $table->string('user');
            $table->dateTime('connect');
            $table->dateTime('drop');
            $table->text('note');
            $table->integer('sshd');
            $table->string('uid');
            
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
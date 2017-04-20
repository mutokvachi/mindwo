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
        Schema::create('Logs', function (Blueprint $table) {
            $table->increments('Id');
            $table->dateTimeTz('Timestamp');
            $table->integer('Offset');
            $table->string('User');
            $table->dateTime('Connect');
            $table->dateTime('Drop');
            $table->string('Note');
            $table->integer('SSHD');
            $table->string('UID');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('Logs');
    }
}
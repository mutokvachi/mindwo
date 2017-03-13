<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountdownTimerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_timer', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sys_name');
            $table->text('waiting_text');
            $table->text('success_text')->comment('Text to show after countdown');
            $table->dateTime('deadline')->comment('Countdown deadline');
            $table->dateTime('show_from')->comment('When start to show timer');
            $table->dateTime('show_to')->comment('How long should show timer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dx_timer');
    }
}

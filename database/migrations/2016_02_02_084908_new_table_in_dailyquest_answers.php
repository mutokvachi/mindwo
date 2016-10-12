<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewTableInDailyquestAnswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_dailyquest_answers', function (Blueprint $table) {
            $table->increments('id');

            $table->string('client_ip', 45)->comment = "Klienta IP adrese";
            $table->integer('dailyquest_option_id')->unsigned();

            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();

            $table->index('client_ip');
            $table->index('dailyquest_option_id');
            $table->foreign('dailyquest_option_id')->references('id')->on('in_dailyquest_options');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_dailyquest_answers', function (Blueprint $table) {
            $table->dropForeign(['dailyquest_option_id']);
        });
        Schema::dropIfExists('in_dailyquest_answers');
    }

}

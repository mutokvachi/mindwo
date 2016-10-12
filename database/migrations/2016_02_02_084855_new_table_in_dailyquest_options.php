<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewTableInDailyquestOptions extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_dailyquest_options', function (Blueprint $table) {
            $table->increments('id');

            $table->string('option_text', 1000)->comment = "Atbilžu variants";
            $table->integer('dailyquest_question_id')->unsigned();

            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();

            $table->index('dailyquest_question_id');
            $table->foreign('dailyquest_question_id')->references('id')->on('in_dailyquest_questions');
        });

        $question_id = DB::table('in_dailyquest_questions')->insertGetId(
                ['question' => 'Kādas ir tavas domas par jauno portālu?', 'is_active' => 1, 'is_multi_answer' => 0]
                , 'id');

        DB::table('in_dailyquest_options')->insert([
            ['option_text' => 'Ļoti patīk!', 'dailyquest_question_id' => $question_id],
            ['option_text' => 'Ir pieņemams', 'dailyquest_question_id' => $question_id],
            ['option_text' => 'Nepatīk', 'dailyquest_question_id' => $question_id]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_dailyquest_options', function (Blueprint $table) {
            $table->dropForeign(['dailyquest_question_id']);
        });
        Schema::dropIfExists('in_dailyquest_options');
    }

}

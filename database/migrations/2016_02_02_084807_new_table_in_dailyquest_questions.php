<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewTableInDailyquestQuestions extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_dailyquest_questions', function (Blueprint $table) {
            $table->increments('id');

            $table->string('question', 1000)->comment = "Jautājuma teksts";
            $table->boolean('is_active')->nullable()->comment = "Ir aktīvs jautājums";
            $table->datetime('date_from')->nullable()->comment = "Attēlošanas sākuma datums";
            $table->datetime('date_to')->nullable()->comment = "Attēlošanas noslēguma datums";
            $table->boolean('is_multi_answer')->nullable()->comment = "Ir iespējamas vairākas atbildes";
            $table->string('picture_name', 500)->nullable()->comment = "Jautājuma attēla nosaukums";
            $table->string('picture_guid', 50)->nullable()->comment = "Jautājuma attēla identifikators";
            $table->integer('source_id')->nullable()->comment = "Datu avots";

            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();

            $table->index('source_id');
            $table->foreign('source_id')->references('id')->on('in_sources');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_dailyquest_questions', function (Blueprint $table) {
            $table->dropForeign(['source_id']);
        });
        Schema::dropIfExists('in_dailyquest_questions');
    }

}

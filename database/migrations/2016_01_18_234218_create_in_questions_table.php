<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('source_id')->nullable();
            $table->text('question')->nullable();
            $table->string('email', 250)->nullable();
            $table->datetime('asked_time')->nullable();
            $table->datetime('answer_time')->nullable();
            
            $table->index('source_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_questions');
    }
}

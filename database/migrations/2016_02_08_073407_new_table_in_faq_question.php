<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewTableInFaqQuestion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_faq_question', function (Blueprint $table) {
            $table->increments('id');

            $table->boolean('is_active')->nullable()->comment = "Ir aktīvs jautājums";
            $table->string('question', 2000)->comment = "Jautājuma teksts";
            $table->string('answer', 4000)->comment = "Atbilde";            
            $table->integer('faq_section_id')->unsigned()->comment = "Nodaļas identifikators";

            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();

            $table->index('faq_section_id');
            $table->foreign('faq_section_id')->references('id')->on('in_faq_section');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_faq_question', function (Blueprint $table) {
            $table->dropForeign(['faq_section_id']);
        });
        Schema::dropIfExists('in_faq_question');
    }
}

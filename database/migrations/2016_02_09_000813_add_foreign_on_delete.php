<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignOnDelete extends Migration
{

    /**
     * Pievieno kaskādes dzēšanu.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_dailyquest_options', function (Blueprint $table) {
            $table->dropForeign(['dailyquest_question_id']);
            $table->foreign('dailyquest_question_id')->references('id')->on('in_dailyquest_questions')->onDelete('cascade');
        });

        Schema::table('in_dailyquest_answers', function (Blueprint $table) {
            $table->dropForeign(['dailyquest_option_id']);
            $table->foreign('dailyquest_option_id')->references('id')->on('in_dailyquest_options')->onDelete('cascade');
        });

        Schema::table('in_faq_question', function (Blueprint $table) {
            $table->dropForeign(['faq_section_id']);
            $table->foreign('faq_section_id')->references('id')->on('in_faq_section')->onDelete('cascade');
        });

        Schema::table('in_faq_section_source', function (Blueprint $table) {
            $table->dropForeign(['faq_section_id']);
            $table->foreign('faq_section_id')->references('id')->on('in_faq_section')->onDelete('cascade');
        });
    }

    /**
     * Noņem kaskādes dzēšanu
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_dailyquest_options', function (Blueprint $table) {
            $table->dropForeign(['dailyquest_question_id']);
            $table->foreign('dailyquest_question_id')->references('id')->on('in_dailyquest_options');
        });

        Schema::table('in_dailyquest_answers', function (Blueprint $table) {
            $table->dropForeign(['dailyquest_option_id']);
            $table->foreign('dailyquest_option_id')->references('id')->on('in_dailyquest_answers');
        });

        Schema::table('in_faq_question', function (Blueprint $table) {
            $table->dropForeign(['faq_section_id']);
            $table->foreign('faq_section_id')->references('id')->on('in_faq_question');
        });

        Schema::table('in_faq_section_source', function (Blueprint $table) {
            $table->dropForeign(['faq_section_id']);
            $table->foreign('faq_section_id')->references('id')->on('in_faq_section_source');
        });
    }

}

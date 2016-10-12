<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveLanguageFieldsInArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_articles', function (Blueprint $table) {
            $table->dropColumn('title_en');
            $table->dropColumn('title_ru');
            $table->dropColumn('article_text_en');
            $table->dropColumn('article_text_ru');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_articles', function (Blueprint $table) {
            $table->string('title_en', 100)->nullable();
            $table->string('title_ru', 100)->nullable();
            $table->text('article_text_en')->nullable();
            $table->text('article_text_ru')->nullable();
        });
    }
}

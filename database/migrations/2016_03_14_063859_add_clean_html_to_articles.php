<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCleanHtmlToArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_articles', function (Blueprint $table) {
            $table->text('article_text_dx_clean')->nullable()->comment = "Ziņas teksts bez HTML";
        });
        
        Schema::table('dx_lists_fields', function (Blueprint $table) {
            $table->boolean('is_clean_html')->nullable()->default(0)->comment = "Ir HTML tīrīšana";
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
            $table->dropColumn(['article_text_dx_clean']);
        });
        
        Schema::table('dx_lists_fields', function (Blueprint $table) {
            $table->dropColumn(['is_clean_html']);
        });
    }
}

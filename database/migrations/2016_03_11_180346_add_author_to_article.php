<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAuthorToArticle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_articles', function (Blueprint $table) {
            $table->integer('author_id')->nullable()->unsigned()->comment = "Ziņas autors";
            
            $table->index('author_id');
            $table->foreign('author_id')->references('id')->on('in_employees');
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
            $table->dropForeign(['author_id']);   
            $table->dropColumn(['in_articles']);
        });
    }
}

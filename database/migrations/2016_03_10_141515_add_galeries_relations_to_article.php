<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGaleriesRelationsToArticle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_articles', function (Blueprint $table) {
            $table->integer('video_galery_id')->nullable()->unsigned()->comment = "Video galerija";
            $table->integer('picture_galery_id')->nullable()->unsigned()->comment = "Attēlu galerija";
            
            $table->index('video_galery_id');
            $table->foreign('video_galery_id')->references('id')->on('in_articles');
            
            $table->index('picture_galery_id');
            $table->foreign('picture_galery_id')->references('id')->on('in_articles');
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
            $table->dropColumn(['video_galery_id']);
            $table->dropColumn(['picture_galery_id']);
        });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInArticlesVid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_articles_vid', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('article_id')->nullable()->unsigned()->comment = "Galerija";
            $table->string('title', 200)->nullable()->comment = "Nosaukums";
            $table->string('prev_file_name', 500)->nullable()->comment = "Attēla datne";
            $table->string('prev_file_guid', 50)->nullable();
            $table->string('file_name', 500)->nullable()->comment = "Video datne";
            $table->string('file_guid', 50)->nullable();
            $table->string('youtube_url', 1000)->nullable()->comment = "YouTube saite";
            $table->integer('order_index')->default(0)->comment = "Secība";  
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('article_id');
        });
        
        Schema::table('in_articles_vid', function (Blueprint $table) {
            $table->foreign('article_id')->references('id')->on('in_articles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_articles_vid');
    }
}

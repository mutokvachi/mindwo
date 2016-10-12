<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_articles_files', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('article_id')->nullable()->unsigned()->comment = "Ziņa";
            $table->string('title', 300)->nullable()->coment = "Nosaukums";
            $table->string('file_name', 500)->nullable()->comment = "Datne";
            $table->string('file_guid', 500)->nullable()->comment = "Datnes unikālais GUID";
            $table->integer('order_index')->default(0)->comment = "Secība";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('order_index');
            
            $table->index('article_id');
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
        Schema::dropIfExists('in_articles_files');
    }
}

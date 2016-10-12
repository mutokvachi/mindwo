<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesContents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('in_articles_contents', function (Blueprint $table) {
            $table->increments('id');            
            $table->string('title', 100)->nullable()->comment = "Nosaukums";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
         DB::table('in_articles_contents')->insert([
            ['title' => 'Teksts'],
            ['title' => 'Ārējā saite'],
            ['title' => 'Datne'],
        ]);
        
        Schema::table('in_articles', function (Blueprint $table) {
            
            $table->integer('content_id')->nullable()->default(1)->unsigned()->comment = "Ziņas veids";
            
            $table->string('alternate_url', 250)->nullable()->comment = "Ziņas norāde";
            $table->string('outer_url', 1000)->nullable()->comment = "Ārējā saite";
            
            $table->string('dwon_file_name', 500)->nullable()->comment = "Lejuplādējamā datne";
            $table->string('dwon_file_guid', 50)->nullable()->comment = "Lejuplādējamās datnes GUID";
            
            $table->text('article_text')->nullable()->change();
            $table->boolean('is_static')->nullable()->default(0)->change();
            $table->boolean('is_searchable')->nullable()->change();
            
            $table->index('content_id');
            $table->foreign('content_id')->references('id')->on('in_articles_contents');
            
            $table->unique('alternate_url');
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
            $table->dropForeign(['content_id']);   
            $table->dropColumn(['content_id']);
            
            $table->dropColumn(['alternate_url']);
            $table->dropColumn(['outer_url']);
            $table->dropColumn(['dwon_file_name']);
            $table->dropColumn(['dwon_file_guid']);
            
            $table->text('article_text')->nullable(false)->change();
            $table->boolean('is_static')->nullable(false)->change();
            $table->boolean('is_searchable')->nullable(false)->change();
        });
        
        Schema::dropIfExists('in_articles_contents');
    }
}

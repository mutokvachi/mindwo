<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InTagsArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_tags_article', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('article_id');
            $table->integer('tag_id');

            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });


        DB::table('in_tags_article')->insert([
            ['article_id' => 1, 'tag_id' => 6 ],
            ['article_id' => 1, 'tag_id' => 5 ],

            ['article_id' => 2, 'tag_id' => 8 ],
            ['article_id' => 2, 'tag_id' => 6 ],
            ['article_id' => 2, 'tag_id' => 5 ],
            ['article_id' => 2, 'tag_id' => 4 ],

            ['article_id' => 3, 'tag_id' => 5 ],
            ['article_id' => 3, 'tag_id' => 12 ],
            ['article_id' => 3, 'tag_id' => 14 ],

            ['article_id' => 4, 'tag_id' => 6 ],
            ['article_id' => 4, 'tag_id' => 5 ],
            ['article_id' => 4, 'tag_id' => 1 ],
            ['article_id' => 4, 'tag_id' => 8 ]
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_tags_article');
    }
}
<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewTableInArticlesImg extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_articles_img', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('article_id')->nullable();
            $table->string('file_name', 500)->nullable();
            $table->string('file_guid', 50)->nullable();
            $table->integer('order_index')->default(0);  
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('article_id');
        });
        
        DB::table('in_articles_img')->insert([
            ['article_id' => 3, 'file_name' => '6EC38DF5-5296-42A2-A9A6-2DCD6E67EEA9.jpg', 'file_guid' => '6EC38DF5-5296-42A2-A9A6-2DCD6E67EEA9.jpg'],
            ['article_id' => 3, 'file_name' => '7b6d8fe8-39b9-45e7-b906-eba61fdb097e.jpg', 'file_guid' => '7b6d8fe8-39b9-45e7-b906-eba61fdb097e.jpg'],
            ['article_id' => 3, 'file_name' => '8BCDD7F7-5924-4035-9491-97A1896608C9.jpg', 'file_guid' => '8BCDD7F7-5924-4035-9491-97A1896608C9.jpg'],
            ['article_id' => 3, 'file_name' => '2AFC2717-ABCB-4B7B-992B-9BCF6B008093.jpg', 'file_guid' => '2AFC2717-ABCB-4B7B-992B-9BCF6B008093.jpg'],
            ['article_id' => 3, 'file_name' => 'b42750b4-3d71-43d4-8e26-9dc4a6a8073d.jpg', 'file_guid' => 'b42750b4-3d71-43d4-8e26-9dc4a6a8073d.jpg'],
            ['article_id' => 3, 'file_name' => 'B1A0BF78-FC5C-4FB8-B795-C080BA3308AB.jpg', 'file_guid' => 'B1A0BF78-FC5C-4FB8-B795-C080BA3308AB.jpg'],
            ['article_id' => 3, 'file_name' => 'E88C87FC-4639-48CE-B97A-E55E8C0BABF5.jpg', 'file_guid' => 'E88C87FC-4639-48CE-B97A-E55E8C0BABF5.jpg'],
            ['article_id' => 3, 'file_name' => 'FF67B4CC-667B-4AFB-B33B-9E21EEE736B4.jpg', 'file_guid' => 'FF67B4CC-667B-4AFB-B33B-9E21EEE736B4.jpg'],
            ['article_id' => 3, 'file_name' => 'c206df61-a98e-45a1-be8a-7398ea6d2430.png', 'file_guid' => 'c206df61-a98e-45a1-be8a-7398ea6d2430.png'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_articles_img');
    }
}

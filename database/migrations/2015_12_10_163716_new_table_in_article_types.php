<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewTableInArticleTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_article_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 10)->nullable();
            $table->string('name', 100)->nullable();
            $table->string('picture_name', 1000)->nullable();
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();

        });

        DB::table('in_article_types')->insert([
            ['code' => 'text', 'name' => 'Teksts', 'picture_name' => 'article_type1.png'],
            ['code' => 'personal', 'name' => 'Personāla jaunumi', 'picture_name' => 'article_type2.png'],
            ['code' => 'img', 'name' => 'Attēls', 'picture_name' => 'article_type3.png'],
            ['code' => 'vid', 'name' => 'Video', 'picture_name' => 'article_type4.png']
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_article_types');
    }
}

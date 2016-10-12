<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateArticleTypePictures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('in_article_types')
            ->where('id', 1)
            ->update(['picture_name' => '']);

        DB::table('in_article_types')
            ->where('id', 2)
            ->update(['picture_name' => 'fa-users']);

        DB::table('in_article_types')
            ->where('id', 3)
            ->update(['picture_name' => 'fa-picture-o']);

        DB::table('in_article_types')
            ->where('id', 4)
            ->update(['picture_name' => 'fa-video-camera']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

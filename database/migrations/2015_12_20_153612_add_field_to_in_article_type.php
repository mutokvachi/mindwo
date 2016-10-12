<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToInArticleType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_article_types', function (Blueprint $table) {
            $table->string('file_name', 500)->nullable();
            $table->string('file_guid', 50)->nullable();
            $table->string('hover_hint', 500)->nullable();
        });
        
        DB::table('in_article_types')
            ->where('id', 1)
            ->update(['file_name' => 'article_placeholder.jpg', 'file_guid' => 'article_placeholder.jpg', 'hover_hint' => 'Ziņa']);
        
        DB::table('in_article_types')
            ->where('id', 2)
            ->update(['file_name' => 'personal_placeholder.jpg', 'file_guid' => 'personal_placeholder.jpg', 'hover_hint' => 'Personāla ziņa']);
        
        DB::table('in_article_types')
            ->where('id', 3)
            ->update(['file_name' => 'picture_placeholder.jpg', 'file_guid' => 'picture_placeholder.jpg', 'hover_hint' => 'Attēlu galerija']);
        
        DB::table('in_article_types')
            ->where('id', 4)
            ->update(['file_name' => 'video_placeholder.jpg', 'file_guid' => 'video_placeholder.jpg', 'hover_hint' => 'Video galerija']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_article_types', function (Blueprint $table) {
            $table->dropColumn('file_name');
            $table->dropColumn('file_guid');
            $table->dropColumn('hover_hint');
        });
    }
}

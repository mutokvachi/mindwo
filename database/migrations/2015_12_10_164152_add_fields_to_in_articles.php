<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToInArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_articles', function (Blueprint $table) {

            $table->integer('type_id')->default(1);
            $table->boolean('is_static');
            $table->boolean('is_searchable')->default(true);
        });

        DB::table('in_articles')
            ->where('id', 1)
            ->update(['type_id' => 1]);
        DB::table('in_articles')
            ->where('id', 2)
            ->update(['type_id' => 2]);
        DB::table('in_articles')
            ->where('id', 3)
            ->update(['type_id' => 3]);
        DB::table('in_articles')
            ->where('id', 4)
            ->update(['type_id' => 4]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_articles', function (Blueprint $table) {
            $table->dropColumn('type_id');
            $table->dropColumn('is_static');
            $table->dropColumn('is_searchable');
        });
    }
}

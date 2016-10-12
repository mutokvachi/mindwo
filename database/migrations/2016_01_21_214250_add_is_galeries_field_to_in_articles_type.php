<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsGaleriesFieldToInArticlesType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_article_types', function (Blueprint $table) {
            $table->boolean('is_for_galeries')->default(0)->nullable();
        });
        
        DB::table('in_article_types')->whereIn('id', [3,4])->update(['is_for_galeries' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_article_types', function (Blueprint $table) {
            $table->dropColumn('is_for_galeries');
        });
    }
}

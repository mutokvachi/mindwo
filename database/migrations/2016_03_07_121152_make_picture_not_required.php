<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakePictureNotRequired extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_articles', function (Blueprint $table) {
            $table->string('picture_name', 500)->nullable()->change();
            $table->string('picture_guid', 100)->nullable()->change();
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
            $table->string('picture_name', 500)->nullable(false)->change();
            $table->string('picture_guid', 100)->nullable(false)->change();
        });
    }
}

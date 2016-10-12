<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContentBgColorToDxPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_pages', function (Blueprint $table) {
            $table->string('content_bg_color', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_pages', function (Blueprint $table) {
            $table->dropColumn('content_bg_color');
        });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBackgroundImgFieldDxPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_pages', function (Blueprint $table) {
            $table->string('file_name', 500)->nullable();
            $table->string('file_guid', 50)->nullable();
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
            $table->dropColumn('file_name');
            $table->dropColumn('file_guid');
        });
    }
}

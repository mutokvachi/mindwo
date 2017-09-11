<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxMenuAddHead extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_menu', function (Blueprint $table) {
            $table->string('head_title', 100)->nullable()->default(null)->comment = trans('db_dx_menu.head_title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_menu', function (Blueprint $table) {
            $table->dropColumn(['head_title']);
        });
    }
}

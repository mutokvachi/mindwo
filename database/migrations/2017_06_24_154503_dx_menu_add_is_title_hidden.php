<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxMenuAddIsTitleHidden extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_menu', function (Blueprint $table) {
            $table->boolean('is_title_hidden')->nullable()->default(false)->comment = trans('db_dx_menu.is_title_hidden');
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
            $table->dropColumn(['is_title_hidden']);
        });
    }
}

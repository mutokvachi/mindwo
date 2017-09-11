<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUiThemesAddCode extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_ui_themes', function (Blueprint $table) {
            $table->string('code', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_ui_themes', function (Blueprint $table) {
            $table->dropColumn(['code']);
        });
    }
}

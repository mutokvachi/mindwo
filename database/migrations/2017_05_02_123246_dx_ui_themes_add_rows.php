<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUiThemesAddRows extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_users')->update(['ui_theme_id' => null]);
        DB::table('dx_ui_themes')->delete();
        DB::statement("INSERT INTO `dx_ui_themes` VALUES (1,'Blue','','',1,NULL,'2017-04-27 16:50:09',NULL,'2017-04-27 16:50:10'),(2,'Green','elix_colors_bamboo.css','elix_colors_bamboo.css',0,NULL,'2017-04-27 17:17:31',NULL,'2017-04-27 17:17:33');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_users')->update(['ui_theme_id' => null]);
        DB::table('dx_ui_themes')->delete();
    }
}

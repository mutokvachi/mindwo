<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxViewsSetUniqTitle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_views', function (Blueprint $table) {
            $table->unique(['title', 'me_user_id'], 'dx_views_title_me_user_id_uniq');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('dx_views', function (Blueprint $table) {
            $table->dropUnique('dx_views_title_me_user_id_uniq');
        });
    }
}

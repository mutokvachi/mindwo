<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxViewsAddGroupId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_views', function (Blueprint $table) {
            $table->integer('group_id')->unsigned()->nullable()->comment = trans('db_dx_views.group');
            
            $table->index('group_id');            
            $table->foreign('group_id')->references('id')->on('dx_views_reports_groups');
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
            $table->dropForeign(['group_id']);
            $table->dropColumn(['group_id']);
        });
    }
}

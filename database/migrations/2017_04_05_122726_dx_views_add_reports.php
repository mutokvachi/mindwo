<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxViewsAddReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_views', function (Blueprint $table) {
            $table->boolean('is_report')->default(false)->comment = trans('db_dx_views.is_report');
            $table->boolean('is_builtin')->default(false)->comment = trans('db_dx_views.is_builtin');
            $table->integer('filter_field_id')->nullable()->comment = trans('db_dx_views.filter_field_id');
            
            $table->index('filter_field_id');            
            $table->foreign('filter_field_id')->references('id')->on('dx_lists_fields');
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
            $table->dropColumn(['is_report']);            
            $table->dropColumn(['is_builtin']);
            
            $table->dropForeign(['filter_field_id']);
            $table->dropColumn(['filter_field_id']);
        });
    }
}

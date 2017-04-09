<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxViewsReportGroupsCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_views_reports_groups');
        
        Schema::create('dx_views_reports_groups', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 100)->comment = trans('db_dx_views_reports_groups.title');
            $table->string('icon', 100)->nullable()->comment = trans('db_dx_views_reports_groups.icon');
            $table->integer('order_index')->default(0)->comment = trans('db_dx_views_reports_groups.order_index');
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_views_reports_groups');
    }
}

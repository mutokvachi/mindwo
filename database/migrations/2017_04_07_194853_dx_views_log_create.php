<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxViewsLogCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_views_log');
        
        Schema::create('dx_views_log', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('view_id')->comment = trans('db_dx_views_log.view');
            $table->integer('user_id')->comment = trans('db_dx_views_log.user');
            $table->datetime('view_time')->comment = trans('db_dx_views_log.view_time');
            
            $table->index('user_id');  
            $table->index('view_time');
            
            $table->index('view_id');            
            $table->foreign('view_id')->references('id')->on('dx_views')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_views_log');
    }
}

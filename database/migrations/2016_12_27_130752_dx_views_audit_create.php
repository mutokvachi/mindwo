<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxViewsAuditCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_views_audit');
        
        Schema::create('dx_views_audit', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->integer('view_id')->comment = "View";  
            $table->datetime('audit_time')->comment = "Audit time";
            $table->integer('record_count')->comment = "Record count";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
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
        Schema::dropIfExists('dx_views_audit');
    }
}

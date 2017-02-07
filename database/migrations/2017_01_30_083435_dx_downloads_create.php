<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxDownloadsCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_downloads');
        
        Schema::create('dx_downloads', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->comment = "Employee";
            $table->integer('field_id')->comment = "File field";
            $table->integer('item_id')->comment = "Item ID";
                                    
            $table->string('guid', 100)->unique()->comment = "Download guid";                        
            
            $table->datetime('init_time')->comment = "Initialized";            
            $table->datetime('last_download_time')->nullable()->comment = "Last download";
            $table->datetime('last_save_time')->nullable()->comment = "Last saved";
            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users');
            
            $table->index('field_id');            
            $table->foreign('field_id')->references('id')->on('dx_lists_fields');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_downloads');
    }
}

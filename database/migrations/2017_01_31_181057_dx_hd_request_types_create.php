<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxHdRequestTypesCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_hd_request_types');
        
        Schema::create('dx_helpdesk_requests', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('parent_id')->unsigned()->nullable()->comment = "Saistītais ieraksts";                                                
            $table->string('title', 200)->comment = "Nosaukums";                        
            $table->integer('order_index')->comment = "Secība";
            $table->text('full_path')->nullable()->comment = "Pilnais nosaukums";
            
            $table->index('parent_id');            
            $table->foreign('parent_id')->references('id')->on('dx_hd_request_types');     
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_hd_request_types');
    }
}

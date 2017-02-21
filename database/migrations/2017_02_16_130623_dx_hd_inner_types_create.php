<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxHdInnerTypesCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_hd_inner_types');
        
        Schema::create('dx_hd_inner_types', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 200)->comment = "Nosaukums";
            
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
        Schema::dropIfExists('dx_hd_inner_types');
    }
}

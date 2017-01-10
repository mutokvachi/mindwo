<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersSuperviseCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_users_supervise');
        
        Schema::create('dx_users_supervise', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->integer('user_id')->comment = "User";
            $table->integer('supervise_id')->unsigned()->comment = "Supervision domain";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users')->onDelete('cascade');
            
            $table->index('supervise_id');            
            $table->foreign('supervise_id')->references('id')->on('dx_supervise');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_users_supervise');
    }
}

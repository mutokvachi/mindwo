<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersPolygraphCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_users_polygraph');
        
        Schema::create('dx_users_polygraph', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->comment = "Employee";
            $table->string('file_name', 500)->nullable()->comment = "File";
            $table->string('file_guid', 100)->nullable()->comment = "File guid";
            $table->date('result_date')->nullable()->comment = "Date measured";
            
            $table->text('notes')->nullable()->comment = "Notes";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_users_polygraph');
    }
}

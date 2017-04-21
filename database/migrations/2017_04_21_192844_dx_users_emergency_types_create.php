<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersEmergencyTypesCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_users_emergency_types');
        
        Schema::create('dx_users_emergency_types', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->increments('id');            
            $table->string('title', 100)->comment = trans('db_dx_users_emergency_types.title');
            
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
        Schema::dropIfExists('dx_users_emergency_types');
    }
}

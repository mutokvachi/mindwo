<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEntityToDxUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_users_entities', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 500)->nullable()->comment = "Title";
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        Schema::create('dx_users_access', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->integer('user_id')->nullable()->comment = "Employee";
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('dx_users');
            
            $table->integer('role_id')->nullable()->comment = "Role";
            $table->index('role_id');
            $table->foreign('role_id')->references('id')->on('dx_roles');
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        DB::table('dx_users_entities')->insert(['id' => 1, 'title' => 'BitFury 1']);
        DB::table('dx_users_entities')->insert(['id' => 2, 'title' => 'BitFury 2']);
        
        Schema::table('dx_users', function (Blueprint $table) {
            $table->integer('entity_id')->nullable()->unsigned()->comment = "Legal Entity";            
            $table->index('entity_id');
            $table->foreign('entity_id')->references('id')->on('dx_users_entities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_users', function (Blueprint $table) {
            $table->dropForeign(['entity_id']);
            $table->dropColumn(['entity_id']);
        });
        
        Schema::dropIfExists('dx_users_entities');
        Schema::dropIfExists('dx_users_access');
    }
}

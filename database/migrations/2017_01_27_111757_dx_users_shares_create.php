<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersSharesCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_users_shares');
        
        Schema::create('dx_users_shares', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->comment = "Employee";
            $table->integer('entity_id')->unsigned()->comment = "Legal entity";
            
            $table->decimal('shares', 5, 2)->default(0)->comment = "Shares (%)";
            $table->string('file_name', 500)->nullable()->comment = "Agreement";
            $table->string('file_guid', 100)->nullable()->comment = "Agreement guid";
            $table->date('valid_from')->nullable()->comment = "Valid from";
            $table->date('valid_to')->nullable()->comment = "Valid till";
                        
            $table->text('notes')->nullable()->comment = "Notes";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users');
            
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
        Schema::dropIfExists('dx_users_shares');
    }
}

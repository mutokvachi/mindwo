<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersSalariesCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_users_salaries');
        
        Schema::create('dx_users_salaries', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->comment = "Employee";
            $table->integer('entity_id')->unsigned()->comment = "Legal entity";
                        
            $table->integer('salary_type_id')->unsigned()->comment = "Salary type";
            $table->decimal('salary', 9, 2)->default(0)->comment = "Salary";
            $table->integer('currency_id')->unsigned()->comment = "Currency";
            $table->decimal('annual_salary', 9, 2)->default(0)->comment = "Annual salary";
                        
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
            
            $table->index('currency_id');            
            $table->foreign('currency_id')->references('id')->on('dx_currencies');
            
            $table->index('salary_type_id');            
            $table->foreign('salary_type_id')->references('id')->on('dx_salary_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_users_salaries');
    }
}

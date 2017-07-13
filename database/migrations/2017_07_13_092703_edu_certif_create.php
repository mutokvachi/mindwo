<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduCertifCreate extends Migration
{ 
    private $table_name = "edu_certif";
    
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists($this->table_name);
        
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');            
            
            $table->integer('user_id')->nullable()->comment = trans($this->table_name.'.user_id');
            $table->integer('programm_id')->unsigned()->nullable()->comment = trans($this->table_name.'.programm_id');
            $table->integer('subject_id')->unsigned()->nullable()->comment = trans($this->table_name.'.subject_id');
            $table->string('reg_nr', 50)->comment = trans($this->table_name.'.reg_nr');
            $table->date('reg_date')->comment = trans($this->table_name.'.reg_date');
            $table->string('file_name', 500)->nullable()->comment = trans($this->table_name.'.file_name');
            $table->string('file_guid', 50)->nullable()->comment = trans($this->table_name.'.file_guid');
            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users');
            
            $table->index('programm_id');            
            $table->foreign('programm_id')->references('id')->on('edu_programms');
            
            $table->index('subject_id');            
            $table->foreign('subject_id')->references('id')->on('edu_subjects');
            
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
        Schema::dropIfExists($this->table_name);
    }
}

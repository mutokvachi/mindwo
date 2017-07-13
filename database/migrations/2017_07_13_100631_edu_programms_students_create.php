<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduProgrammsStudentsCreate extends Migration
{
    private $table_name = "edu_programms_students";
    
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
            
            $table->integer('programm_id')->unsigned()->comment = trans($this->table_name.'.programm_id');
            $table->integer('student_id')->comment = trans($this->table_name.'.student_id');
            $table->integer('org_id')->unsigned()->nullable()->comment = trans($this->table_name.'.org_id');
            $table->datetime('applay_time')->comment = trans($this->table_name.'.applay_time');
            $table->boolean('is_approved')->nullable()->default(false)->comment = trans($this->table_name.'.is_approved');
            $table->integer('credit_points_earned')->nullable()->default(0)->comment = trans($this->table_name.'.credit_points_earned');
            $table->datetime('droped_time')->nullable()->comment = trans($this->table_name.'.droped_time');
                        
            $table->index('programm_id');            
            $table->foreign('programm_id')->references('id')->on('edu_programms');
            
            $table->index('org_id');            
            $table->foreign('org_id')->references('id')->on('edu_orgs');
            
            $table->index('student_id');            
            $table->foreign('student_id')->references('id')->on('dx_users');
            
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

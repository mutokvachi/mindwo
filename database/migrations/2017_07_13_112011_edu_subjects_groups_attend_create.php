<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduSubjectsGroupsAttendCreate extends Migration
{
    private $table_name = "edu_subjects_groups_attend";
    
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
            
            $table->integer('group_day_id')->unsigned()->comment = trans('db_' . $this->table_name.'.group_day_id');
            $table->integer('student_id')->comment = trans('db_' . $this->table_name.'.student_id');
                        
            $table->index('group_day_id');            
            $table->foreign('group_day_id')->references('id')->on('edu_subjects_groups_days');
            
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

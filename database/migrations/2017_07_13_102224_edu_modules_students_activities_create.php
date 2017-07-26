<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduModulesStudentsActivitiesCreate extends Migration
{
    private $table_name = "edu_modules_students_activities";
    
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
            
            $table->integer('module_student_id')->unsigned()->comment = trans('db_' . $this->table_name.'.module_student_id');
            $table->integer('activity_id')->unsigned()->comment = trans('db_' . $this->table_name.'.activity_id');
            $table->text('notes')->nullable()->comment = trans('db_' . $this->table_name.'.notes');
            
            $table->index('module_student_id');            
            $table->foreign('module_student_id')->references('id')->on('edu_modules_students');
            
            $table->index('activity_id');            
            $table->foreign('activity_id')->references('id')->on('edu_activities');
            
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

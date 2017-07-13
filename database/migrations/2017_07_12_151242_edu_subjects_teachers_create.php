<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduSubjectsTeachersCreate extends Migration
{
    private $table_name = "edu_subjects_teachers";
    
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
            
            $table->integer('subject_id')->unsigned()->comment = trans($this->table_name.'.subject_id');
            $table->integer('teacher_id')->comment = trans($this->table_name.'.teacher_id');
            $table->boolean('is_content_rights')->nullable()->default(false)->comment = trans($this->table_name.'.is_content_rights');
            
            $table->index('subject_id');            
            $table->foreign('subject_id')->references('id')->on('edu_subjects');
            
            $table->index('teacher_id');            
            $table->foreign('teacher_id')->references('id')->on('dx_users');
            
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

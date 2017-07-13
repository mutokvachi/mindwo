<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduSubjectsGroupsDaysCreate extends Migration
{ 
    private $table_name = "edu_subjects_groups_days";
    
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
            
            $table->integer('group_id')->unsigned()->comment = trans($this->table_name.'.group_id');
            $table->date('lesson_date')->comment = trans($this->table_name.'.lesson_date');
            $table->time('time_from')->comment = trans($this->table_name.'.time_from');
            $table->time('time_to')->comment = trans($this->table_name.'.time_to');
            $table->string('room_nr', 6)->nullable()->comment = trans($this->table_name.'.room_nr');
            $table->text('notes')->nullable()->comment = trans($this->table_name.'.notes');
            
            $table->index('group_id');            
            $table->foreign('group_id')->references('id')->on('edu_subjects_groups');
            
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

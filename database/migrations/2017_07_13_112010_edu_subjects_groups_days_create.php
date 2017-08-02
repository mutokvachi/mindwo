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
            
            $table->string('title', 250)->nullable()->comment = trans('db_' . $this->table_name.'.title');
            $table->integer('group_id')->unsigned()->comment = trans('db_' . $this->table_name.'.group_id');
            
            $table->date('lesson_date')->comment = trans('db_' . $this->table_name.'.lesson_date');
            $table->time('time_from')->comment = trans('db_' . $this->table_name.'.time_from');
            $table->time('time_to')->comment = trans('db_' . $this->table_name.'.time_to');
            $table->integer('room_id')->unsigned()->comment = trans('db_' . $this->table_name.'.room_id');
            $table->string('notes', 500)->nullable()->comment = trans('db_' . $this->table_name.'.notes');
            
            $table->index('group_id');            
            $table->foreign('group_id')->references('id')->on('edu_subjects_groups');
            
            $table->index('room_id');            
            $table->foreign('room_id')->references('id')->on('edu_rooms');
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable(); 
        });    
        
        DB::unprepared("CREATE TRIGGER tr_edu_subjects_groups_days_insert BEFORE INSERT ON  edu_subjects_groups_days FOR EACH ROW 
            BEGIN
                DECLARE room varchar(50);
                                
                SET room = (SELECT title FROM edu_rooms WHERE id = new.room_id);
                                
                SET new.title = CONCAT(DAY(new.lesson_date), '.', MONTH(new.lesson_date), '.', YEAR(new.lesson_date), ' ', new.time_from, ' - ', new.time_to, ' " . trans('db_' . $this->table_name.'.room_title') . " ', room);                
            END;
        ");
        
        DB::unprepared("CREATE TRIGGER tr_edu_subjects_groups_days_update BEFORE UPDATE ON  edu_subjects_groups_days FOR EACH ROW 
            BEGIN
                DECLARE room varchar(50);
                                
                SET room = (SELECT title FROM edu_rooms WHERE id = new.room_id);
                                
                SET new.title = CONCAT(DAY(new.lesson_date), '.', MONTH(new.lesson_date), '.', YEAR(new.lesson_date), ' ', new.time_from, ' - ', new.time_to, ' " . trans('db_' . $this->table_name.'.room_title') . " ', room);                
            END;
        ");
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

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduRoomsCalendarsCreate extends Migration
{
    private $table_name = "edu_rooms_calendars";
    
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
            
            $table->integer('room_id')->unsigned()->comment = trans('db_' . $this->table_name.'.room_id');
            $table->datetime('from_time')->comment = trans('db_' . $this->table_name.'.from_time');
            $table->datetime('to_time')->comment = trans('db_' . $this->table_name.'.to_time');
            $table->integer('subject_group_day_id')->nullable()->unsigned()->comment = trans('db_' . $this->table_name.'.subject_group_day_id');
            $table->string('notes', 500)->nullable()->comment = trans('db_' . $this->table_name.'.notes');
            
            $table->index('room_id');            
            $table->foreign('room_id')->references('id')->on('edu_rooms');
            
            $table->index('subject_group_day_id');            
            $table->foreign('subject_group_day_id')->references('id')->on('edu_subjects_groups_days')->onDelete('cascade');
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable(); 
            
            $table->unique(['room_id', 'from_time', 'to_time']);
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

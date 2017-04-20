<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxMeetingsAgendasCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_meetings_agendas');
        
        Schema::create('dx_meetings_agendas', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('meeting_id')->unsigned()->unsigned()->comment = trans('db_dx_meetings.meeting_type_id');
            $table->integer('order_index')->default(0)->comment = trans('db_dx_meetings.meeting_time');
            $table->string('title', 500)->comment = trans('db_dx_meetings.meeting_type_id');
                        
            $table->index('meeting_id');            
            $table->foreign('meeting_id')->references('id')->on('dx_meetings');
            
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
        Schema::dropIfExists('dx_meetings_agendas');
    }
}

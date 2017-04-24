<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxMeetingsCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('dx_meetings');
        
        Schema::create('dx_meetings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->datetime('meeting_time')->comment = trans('db_dx_meetings.meeting_time');
            $table->integer('meeting_type_id')->unsigned()->unsigned()->comment = trans('db_dx_meetings.meeting_type_id');
            
            $table->string('protocol_file_name', 500)->nullable()->comment = trans('db_dx_meetings.protocol_file_name');
            $table->string('protocol_file_guid', 100)->nullable();            
            $table->text('protocol_file_dx_text')->nullable();
            
            $table->index('meeting_type_id');            
            $table->foreign('meeting_type_id')->references('id')->on('dx_meetings_types');
            
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
        Schema::dropIfExists('dx_meetings');
    }
}

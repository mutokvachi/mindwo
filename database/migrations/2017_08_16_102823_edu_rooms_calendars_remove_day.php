<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduRoomsCalendarsRemoveDay extends Migration
{
    private $table_name = "edu_rooms_calendars";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->dropForeign(['subject_group_day_id']);
            $table->dropIndex(['subject_group_day_id']);
            $table->dropColumn(['subject_group_day_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->integer('subject_group_day_id')->nullable()->unsigned()->comment = trans('db_' . $this->table_name.'.subject_group_day_id');
            
            $table->index('subject_group_day_id');            
            $table->foreign('subject_group_day_id')->references('id')->on('edu_subjects_groups_days')->onDelete('cascade');
        });
    }
}

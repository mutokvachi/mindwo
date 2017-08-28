<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduSubjectsGroupsAddMainTeacher extends Migration
{
    private $table_name = "edu_subjects_groups";
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->integer('main_teacher_id')->nullable()->comment = trans('db_' . $this->table_name.'.main_teacher_id');

            $table->index('main_teacher_id');            
            $table->foreign('main_teacher_id')->references('id')->on('dx_users');
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
            $table->dropColumn(['main_teacher_id']);
        });
    }
}

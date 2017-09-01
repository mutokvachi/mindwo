<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduSubjectsAddHours extends Migration
{
    private $table_name = "edu_subjects";
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->integer('academic_hours')->unsigned()->nullable()->default(0)->comment = trans('db_' . $this->table_name.'.academic_hours');
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
            $table->dropColumn(['academic_hours']);
        });
    }
}

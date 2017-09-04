<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduSubjectsGroupsAddOrgId extends Migration
{
    private $table_name = "edu_subjects_groups_members";
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->integer('org_id')->unsigned()->nullable()->comment = trans('db_' . $this->table_name.'.org_id');

            $table->index('org_id');            
            $table->foreign('org_id')->references('id')->on('edu_orgs');
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
            $table->dropColumn(['org_id']);
        });
    }
}

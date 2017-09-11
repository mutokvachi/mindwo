<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduSubjectsGroupsOrgsCreate extends Migration
{
    private $table_name = "edu_subjects_groups_orgs";
    
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
            
            $table->integer('group_id')->unsigned()->comment = trans('db_' . $this->table_name.'.group_id');
            $table->integer('org_id')->unsigned()->comment = trans('db_' . $this->table_name.'.org_id');
            $table->integer('places_quota')->unsigned()->comment = trans('db_' . $this->table_name.'.places_quota');
            
            $table->index('group_id');            
            $table->foreign('group_id')->references('id')->on('edu_subjects_groups')->onDelete('cascade');
            
            $table->index('org_id');            
            $table->foreign('org_id')->references('id')->on('edu_orgs');
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable(); 
            
            $table->unique(['group_id', 'org_id']);
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

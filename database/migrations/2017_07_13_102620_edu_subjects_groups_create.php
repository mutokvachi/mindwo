<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduSubjectsGroupsCreate extends Migration
{
    private $table_name = "edu_subjects_groups";
    
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
            
            $table->integer('subject_id')->unsigned()->comment = trans($this->table_name.'.subject_id');
            $table->integer('teacher_id')->comment = trans($this->table_name.'.teacher_id');
            $table->integer('seats_limit')->default(0)->comment = trans($this->table_name.'.seats_limit');
            $table->datetime('signup_due')->comment = trans($this->table_name.'.signup_due');
            $table->boolean('is_published')->nullable()->default(false)->comment = trans($this->table_name.'.is_published');
            $table->boolean('is_generated')->nullable()->default(false)->comment = trans($this->table_name.'.is_generated');
            $table->boolean('is_inner_group')->nullable()->default(false)->comment = trans($this->table_name.'.is_inner_group');
            $table->integer('inner_org_id')->unsigned()->nullable()->comment = trans($this->table_name.'.inner_org_id');
            $table->datetime('canceled_time')->nullable()->comment = trans($this->table_name.'.canceled_time');
            $table->text('canceled_reason')->nullable()->comment = trans($this->table_name.'.canceled_reason');
            $table->datetime('approved_time')->nullable()->comment = trans($this->table_name.'.approved_time');
            
            $table->index('subject_id');            
            $table->foreign('subject_id')->references('id')->on('edu_subjects');
            
            $table->index('teacher_id');            
            $table->foreign('teacher_id')->references('id')->on('dx_users');
            
            $table->index('inner_org_id');            
            $table->foreign('inner_org_id')->references('id')->on('edu_orgs');
            
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

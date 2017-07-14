<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduSubjectsCreate extends Migration
{
    private $table_name = "edu_subjects";
    
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
            
            $table->string('title', 250)->comment = trans('db_' . $this->table_name.'.title');
            $table->integer('avail_id')->unsigned()->nullable()->comment = trans('db_' . $this->table_name.'.avail_id');    
            $table->string('subject_code', 50)->comment = trans('db_' . $this->table_name.'.code');
            $table->integer('subject_type_id')->unsigned()->comment = trans('db_' . $this->table_name.'.subject_type_id');
            $table->boolean('is_fee')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_fee');
            $table->decimal('price_for_teacher', 7, 2)->nullable()->default(0)->comment = trans('db_' . $this->table_name.'.price_for_teacher');
            $table->decimal('price_for_rooms', 7, 2)->nullable()->default(0)->comment = trans('db_' . $this->table_name.'.price_for_rooms');
            $table->string('project_code', 50)->comment = trans('db_' . $this->table_name.'.project_code');
            $table->integer('programm_id')->unsigned()->comment = trans('db_' . $this->table_name.'.programm_id');
            $table->integer('credit_points')->nullable()->default(0)->comment = trans('db_' . $this->table_name.'.credit_points');  
            $table->string('learning_url', 1000)->nullable()->comment = trans('db_' . $this->table_name.'.learning_url');  
            $table->text('description')->nullable()->comment = trans('db_' . $this->table_name.'.description');
            $table->text('user_approval_msg')->nullable()->comment = trans('db_' . $this->table_name.'.user_approval_msg');
            $table->integer('info_survey_id')->unsigned()->nullable()->comment = trans('db_' . $this->table_name.'.info_survey_id');
            $table->integer('subject_pretest_id')->unsigned()->nullable()->comment = trans('db_' . $this->table_name.'.test_id');
            $table->boolean('is_subj_qual_test_ok_need')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_subj_qual_test_ok_need');
            $table->boolean('is_progr_qual_test_ok_need')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_progr_qual_test_ok_need');
            $table->boolean('is_org_approve_need')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_org_approve_need');            
            $table->integer('cert_numerator_id')->nullable()->comment = trans('db_' . $this->table_name.'.cert_numerator_id');
            $table->boolean('is_published')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_published');
            
            $table->index('avail_id');            
            $table->foreign('avail_id')->references('id')->on('edu_programms_avail');
            
            $table->index('programm_id');            
            $table->foreign('programm_id')->references('id')->on('edu_programms');
            
            $table->index('subject_type_id');            
            $table->foreign('subject_type_id')->references('id')->on('edu_subjects_types');
            
            $table->index('info_survey_id');            
            $table->foreign('info_survey_id')->references('id')->on('in_tests');
            
            $table->index('subject_pretest_id');            
            $table->foreign('subject_pretest_id')->references('id')->on('in_tests');
            
            $table->index('cert_numerator_id');            
            $table->foreign('cert_numerator_id')->references('id')->on('dx_numerators');
                        
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

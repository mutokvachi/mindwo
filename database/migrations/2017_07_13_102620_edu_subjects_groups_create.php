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
            
            $table->string('title', 1000)->nullable()->comment = trans('db_' . $this->table_name.'.title');
            $table->integer('subject_id')->unsigned()->comment = trans('db_' . $this->table_name.'.subject_id');
            $table->integer('teacher_id')->comment = trans('db_' . $this->table_name.'.teacher_id');
            $table->integer('seats_limit')->default(0)->comment = trans('db_' . $this->table_name.'.seats_limit');
            $table->datetime('signup_due')->comment = trans('db_' . $this->table_name.'.signup_due');
            $table->boolean('is_published')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_published');
            $table->boolean('is_generated')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_generated');
            $table->datetime('approved_time')->nullable()->comment = trans('db_' . $this->table_name.'.approved_time');
            $table->boolean('is_inner_group')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_inner_group');
            $table->integer('inner_org_id')->unsigned()->nullable()->comment = trans('db_' . $this->table_name.'.inner_org_id');
            $table->datetime('canceled_time')->nullable()->comment = trans('db_' . $this->table_name.'.canceled_time');
            $table->string('canceled_reason', 500)->nullable()->comment = trans('db_' . $this->table_name.'.canceled_reason');
                        
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
        
        DB::unprepared("CREATE TRIGGER tr_edu_subjects_groups_insert BEFORE INSERT ON  edu_subjects_groups FOR EACH ROW 
            BEGIN
                DECLARE subj_title varchar(250);
                DECLARE teacher varchar(200);
                
                SET subj_title = (SELECT title FROM edu_subjects WHERE id = new.subject_id);
                SET teacher = (SELECT display_name FROM dx_users WHERE id = new.teacher_id);
                
                SET new.title = CONCAT(subj_title, ' (', teacher, ')');
            END;
        ");
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

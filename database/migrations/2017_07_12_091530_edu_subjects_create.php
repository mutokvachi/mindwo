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
            
            $table->string('title_full', 250)->nullable()->comment = trans('db_' . $this->table_name.'.title_full');
            $table->string('title', 200)->comment = trans('db_' . $this->table_name.'.title');
            $table->integer('subject_type_id')->unsigned()->comment = trans('db_' . $this->table_name.'.subject_type_id');
            $table->integer('avail_id')->unsigned()->comment = trans('db_' . $this->table_name.'.avail_id');
            
            $table->integer('module_id')->unsigned()->comment = trans('db_' . $this->table_name.'.module_id');            
            $table->string('subject_code', 5)->comment = trans('db_' . $this->table_name.'.subject_code');            
            
            $table->boolean('is_org_approve_need')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_org_approve_need'); 
            $table->boolean('is_published')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_published');
                        
            $table->decimal('price_for_student', 7, 2)->nullable()->default(0)->comment = trans('db_' . $this->table_name.'.price_for_student');
            $table->decimal('price_for_teacher', 7, 2)->nullable()->default(0)->comment = trans('db_' . $this->table_name.'.price_for_teacher');
            $table->decimal('price_for_rooms', 7, 2)->nullable()->default(0)->comment = trans('db_' . $this->table_name.'.price_for_rooms');            
             
            $table->string('learning_url', 1000)->nullable()->comment = trans('db_' . $this->table_name.'.learning_url');
                        
            $table->integer('subject_pretest_id')->unsigned()->nullable()->comment = trans('db_' . $this->table_name.'.subject_pretest_id');
            $table->boolean('is_subj_qual_test_ok_need')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_subj_qual_test_ok_need');
            $table->boolean('is_progr_qual_test_ok_need')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_progr_qual_test_ok_need');
            $table->integer('subject_end_test_id')->unsigned()->nullable()->comment = trans('db_' . $this->table_name.'.subject_end_test_id');
            $table->integer('cert_numerator_id')->nullable()->comment = trans('db_' . $this->table_name.'.cert_numerator_id');
                        
            $table->index('avail_id');            
            $table->foreign('avail_id')->references('id')->on('edu_modules_avail');
            
            $table->index('module_id');            
            $table->foreign('module_id')->references('id')->on('edu_modules');
            
            $table->index('subject_type_id');            
            $table->foreign('subject_type_id')->references('id')->on('edu_subjects_types');
                        
            $table->index('subject_pretest_id');            
            $table->foreign('subject_pretest_id')->references('id')->on('in_tests');
            
            $table->index('subject_end_test_id');            
            $table->foreign('subject_end_test_id')->references('id')->on('in_tests');
            
            $table->index('cert_numerator_id');            
            $table->foreign('cert_numerator_id')->references('id')->on('dx_numerators');
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable(); 
        });
        
        $sql_trig = "BEGIN
                        DECLARE cod varchar(50);
                        DECLARE next_id int default 0;
                        
                        SET cod = (SELECT CONCAT(edu_programms.code, '-', edu_modules.code) FROM edu_modules JOIN edu_programms ON edu_modules.programm_id = edu_programms.id WHERE edu_modules.id = new.module_id);
                                                
                        select 
                            auto_increment into next_id
                        from 
                            information_schema.tables
                        where 
                            table_name = 'edu_subjects'
                            and table_schema = database();
     
                        SET new.title_full = CONCAT('[', cod, '-', next_id, '] ', new.title);                
                    END;";
                
        DB::unprepared("CREATE TRIGGER tr_edu_subjects_insert BEFORE INSERT ON edu_subjects FOR EACH ROW " . $sql_trig);
        
        DB::unprepared("CREATE TRIGGER tr_edu_subjects_update BEFORE UPDATE ON edu_subjects FOR EACH ROW " . $sql_trig);
 
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

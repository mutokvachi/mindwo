<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduSubjectsCode extends Migration
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
            $table->unique(['module_id', 'subject_code']);
        });
        
        $sql_trig = "BEGIN                        
                        DECLARE cod varchar(50);
                        
                        IF new.subject_code is null THEN
                            SET new.subject_code = (SELECT count(*) FROM edu_subjects WHERE module_id = new.module_id) + 1;
                        END IF;  
                        
                        SET cod = (SELECT CONCAT(edu_programms.code, '-', edu_modules.code) FROM edu_modules JOIN edu_programms ON edu_modules.programm_id = edu_programms.id WHERE edu_modules.id = new.module_id);
                              
                        SET new.title_full = CONCAT('[', cod, '-', new.subject_code, '] ', new.title);   
                        
                                  
                    END;";
        
        DB::unprepared('DROP TRIGGER `tr_edu_subjects_insert`');
        DB::unprepared('DROP TRIGGER `tr_edu_subjects_update`');
        
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
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->dropUnique('edu_subjects_module_id_subject_code_unique');
        });
        
        DB::unprepared('DROP TRIGGER `tr_edu_subjects_insert`');
        DB::unprepared('DROP TRIGGER `tr_edu_subjects_update`');
        
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
}

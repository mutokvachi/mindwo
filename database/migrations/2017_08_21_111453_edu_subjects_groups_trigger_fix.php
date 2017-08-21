<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduSubjectsGroupsTriggerFix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP TRIGGER `tr_edu_subjects_groups_insert`');
        DB::unprepared('DROP TRIGGER `tr_edu_subjects_groups_update`');
        
        DB::unprepared("CREATE TRIGGER tr_edu_subjects_groups_insert BEFORE INSERT ON  edu_subjects_groups FOR EACH ROW 
            BEGIN               
                DECLARE cod varchar(250);
                DECLARE next_id int default 0;
                 
                select 
                    auto_increment into next_id
                from 
                    information_schema.tables
                where 
                    table_name = 'edu_subjects_groups'
                    and table_schema = database();
                            
                SET cod = (SELECT CONCAT('[', edu_programms.code, '-', edu_modules.code, '-', edu_subjects.subject_code, '] ', edu_subjects.title) FROM edu_subjects JOIN edu_modules on edu_subjects.module_id = edu_modules.id JOIN edu_programms ON edu_modules.programm_id = edu_programms.id WHERE edu_subjects.id = new.subject_id);
                                
                SET new.title = CONCAT('G',next_id,': ',cod);
            END;
        ");
        
        DB::unprepared("CREATE TRIGGER tr_edu_subjects_groups_update BEFORE UPDATE ON  edu_subjects_groups FOR EACH ROW 
            BEGIN
                DECLARE cod varchar(250);
                                            
                SET cod = (SELECT CONCAT('[', edu_programms.code, '-', edu_modules.code, '-', edu_subjects.subject_code, '] ', edu_subjects.title) FROM edu_subjects JOIN edu_modules on edu_subjects.module_id = edu_modules.id JOIN edu_programms ON edu_modules.programm_id = edu_programms.id WHERE edu_subjects.id = new.subject_id);
                                
                SET new.title = CONCAT('G',new.id,': ',cod);
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
        DB::unprepared('DROP TRIGGER `tr_edu_subjects_groups_insert`');
        DB::unprepared('DROP TRIGGER `tr_edu_subjects_groups_update`');
        
        DB::unprepared("CREATE TRIGGER tr_edu_subjects_groups_insert BEFORE INSERT ON  edu_subjects_groups FOR EACH ROW 
            BEGIN               
                DECLARE cod varchar(250);
                DECLARE next_id int default 0;
                 
                select 
                    auto_increment into next_id
                from 
                    information_schema.tables
                where 
                    table_name = 'edu_subjects_groups'
                    and table_schema = database();
                            
                SET cod = (SELECT CONCAT('[', edu_programms.code, '-', edu_modules.code, '-', edu_subjects.id, '] ', edu_subjects.title) FROM edu_subjects JOIN edu_modules on edu_subjects.module_id = edu_modules.id JOIN edu_programms ON edu_modules.programm_id = edu_programms.id WHERE edu_subjects.id = new.subject_id);
                                
                SET new.title = CONCAT('G',next_id,': ',cod);
            END;
        ");
        
        DB::unprepared("CREATE TRIGGER tr_edu_subjects_groups_update BEFORE UPDATE ON  edu_subjects_groups FOR EACH ROW 
            BEGIN
                DECLARE cod varchar(250);
                                            
                SET cod = (SELECT CONCAT('[', edu_programms.code, '-', edu_modules.code, '-', edu_subjects.id, '] ', edu_subjects.title) FROM edu_subjects JOIN edu_modules on edu_subjects.module_id = edu_modules.id JOIN edu_programms ON edu_modules.programm_id = edu_programms.id WHERE edu_subjects.id = new.subject_id);
                                
                SET new.title = CONCAT('G',new.id,': ',cod);
            END;
        ");
    }
}

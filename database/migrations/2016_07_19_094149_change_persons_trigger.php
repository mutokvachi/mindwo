<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePersonsTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `tr_dx_persons_insert`');
        
        DB::unprepared('DROP TRIGGER If EXISTS `tr_dx_persons_update`');
        
        DB::unprepared("CREATE TRIGGER tr_dx_persons_insert BEFORE INSERT ON  dx_persons FOR EACH ROW 
            BEGIN
                IF NEW.is_legal_person THEN
                    SET NEW.search_title = CONCAT(case when NEW.person_type_id is null then '' else CONCAT((select code from dx_persons_types where id=NEW.person_type_id), ' ') end, NEW.title, case when NEW.reg_nr is null then '' else CONCAT(' (', NEW.reg_nr, ')') end);
                    SET NEW.first_name = null;
                    SET NEW.last_name = null;
                ELSE
                    SET NEW.search_title = CONCAT(IFNULL(NEW.first_name,''), ' ', IFNULL(NEW.last_name,''), case when NEW.reg_nr is null then '' else CONCAT(' (', NEW.reg_nr, ')') end);
                    SET NEW.title = CONCAT(IFNULL(NEW.first_name,''), ' ', IFNULL(NEW.last_name,''));
                    SET NEW.person_type_id = null;
                END IF;
                
                IF NEW.is_vat = 0 THEN
                    SET NEW.vat_reg_nr = null;
                END IF;
            END;");
        
        DB::unprepared("CREATE TRIGGER tr_dx_persons_update BEFORE UPDATE ON  dx_persons FOR EACH ROW 
            BEGIN
                IF NEW.is_legal_person THEN
                    SET NEW.search_title = CONCAT(case when NEW.person_type_id is null then '' else CONCAT((select code from dx_persons_types where id=NEW.person_type_id), ' ') end, NEW.title, case when NEW.reg_nr is null then '' else CONCAT(' (', NEW.reg_nr, ')') end);
                    SET NEW.first_name = null;
                    SET NEW.last_name = null;
                ELSE
                    SET NEW.search_title = CONCAT(IFNULL(NEW.first_name,''), ' ', IFNULL(NEW.last_name,''), case when NEW.reg_nr is null then '' else CONCAT(' (', NEW.reg_nr, ')') end);
                    SET NEW.title = CONCAT(IFNULL(NEW.first_name,''), ' ', IFNULL(NEW.last_name,''));
                    SET NEW.person_type_id = null;
                END IF;
                
                IF NEW.is_vat = 0 THEN
                    SET NEW.vat_reg_nr = null;
                END IF;
            END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `tr_dx_persons_insert`');
        
        DB::unprepared('DROP TRIGGER If EXISTS `tr_dx_persons_update`');
    }
}

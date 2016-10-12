<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersTriggerUnblock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `tr_dx_users_update`');
        
        DB::unprepared("CREATE TRIGGER tr_dx_users_update BEFORE UPDATE ON  dx_users FOR EACH ROW 
            BEGIN
                SET NEW.display_name = CONCAT(ifnull(NEW.first_name,''), ' ', ifnull(NEW.last_name,''));
                
IF OLD.is_blocked = 1 AND NEW.is_blocked = 0 THEN BEGIN
                    SET NEW.auth_attempts = 0;
                    SET NEW.last_attempt = null;
                END; END IF;
            END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `tr_dx_users_update`');
    }
}

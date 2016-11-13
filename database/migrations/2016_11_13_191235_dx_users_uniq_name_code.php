<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersUniqNameCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_users', function (Blueprint $table) {
            $table->string('full_name_code', 500)->nullable()->comment = "Employee full name with person ID";

            $table->unique('full_name_code');
        });  
        
        DB::unprepared('DROP TRIGGER IF EXISTS `tr_dx_users_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `tr_dx_users_update`');
        
        DB::unprepared("
            CREATE TRIGGER tr_dx_users_insert BEFORE INSERT ON  dx_users FOR EACH ROW 
            BEGIN
                SET NEW.display_name = CONCAT(ifnull(NEW.first_name,''), ' ', ifnull(NEW.last_name,''));
                SET NEW.full_name_code = CONCAT(ifnull(NEW.first_name,''), ' ', ifnull(NEW.last_name,''), ' ', ifnull(NEW.person_code,''));
            END;
        ");
        
        DB::unprepared("
            CREATE TRIGGER tr_dx_users_update BEFORE UPDATE ON  dx_users FOR EACH ROW 
            BEGIN
                SET NEW.display_name = CONCAT(ifnull(NEW.first_name,''), ' ', ifnull(NEW.last_name,''));
                
                IF OLD.is_blocked = 1 AND NEW.is_blocked = 0 THEN 
                    BEGIN
                        SET NEW.auth_attempts = 0;
                        SET NEW.last_attempt = null;
                    END; 
                END IF;
                
                SET NEW.full_name_code = CONCAT(ifnull(NEW.first_name,''), ' ', ifnull(NEW.last_name,''), ' ', ifnull(NEW.person_code,''));
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
        Schema::table('dx_users', function (Blueprint $table) {
            $table->dropColumn(['full_name_code']);
        });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmplFldsToDxUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_users')->update(['department_id' => null]);
        
        Schema::table('dx_users', function (Blueprint $table) {
            
            $table->integer('manager_id')->nullable()->comment = "Tiešais vadītājs";
            $table->string('office_address', 500)->nullable()->comment = "Biroja adrese";
            $table->string('office_cabinet', 10)->nullable()->comment = "Kabineta nr.";
            $table->date('left_from')->nullable()->comment = "Prombūtnē no";
            $table->date('left_to')->nullable()->comment = "Prombūtnē līdz";
            
            $table->integer('left_reason_id')->nullable()->unsigned()->comment = "Prombūtnes iemesls";
            $table->integer('substit_empl_id')->nullable()->comment = "Aizvietotājs";
            
            $table->string('first_name', 50)->nullable()->comment = "Vārds";
            $table->string('last_name', 50)->nullable()->comment = "Uzvārds";
            $table->string('person_code', 12)->nullable()->comment = "Personas kods";
            
            $table->index('manager_id');
            $table->foreign('manager_id')->references('id')->on('dx_users');
            
            $table->index('left_reason_id');
            $table->foreign('left_reason_id')->references('id')->on('in_left_reasons');
            
            $table->index('substit_empl_id');
            $table->foreign('substit_empl_id')->references('id')->on('dx_users');
                                    
            $table->integer('department_id')->nullable()->unsigned()->change();
            
            $table->foreign('department_id')->references('id')->on('in_departments');
            
            $table->index('office_cabinet');
        });
        
        DB::unprepared("
            CREATE TRIGGER tr_dx_users_insert BEFORE INSERT ON  dx_users FOR EACH ROW 
            BEGIN
                SET NEW.display_name = CONCAT(ifnull(NEW.first_name,''), ' ', ifnull(NEW.last_name,''));
            END;
        ");
        
        DB::unprepared("
            CREATE TRIGGER tr_dx_users_update BEFORE UPDATE ON  dx_users FOR EACH ROW 
            BEGIN
                SET NEW.display_name = CONCAT(ifnull(NEW.first_name,''), ' ', ifnull(NEW.last_name,''));
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
        DB::unprepared('DROP TRIGGER `tr_dx_users_insert`');
        
        DB::unprepared('DROP TRIGGER `tr_dx_users_update`');
        
        Schema::table('dx_users', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropColumn(['manager_id']);
            
            $table->dropForeign(['left_reason_id']);
            $table->dropColumn(['left_reason_id']);
            
            $table->dropForeign(['substit_empl_id']);
            $table->dropColumn(['substit_empl_id']);
            
            $table->dropForeign(['department_id']);
            
            $table->dropColumn(['office_address']);
            $table->dropColumn(['left_from']);
            $table->dropColumn(['left_to']);
            $table->dropColumn(['office_cabinet']);
            $table->dropColumn(['first_name']);
            $table->dropColumn(['last_name']);
            $table->dropColumn(['person_code']);
        });
    }
}

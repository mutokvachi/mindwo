<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDxUsersLeft extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('dx_users_left', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->nullable()->comment = "Darbinieks";
            $table->date('left_from')->nullable()->comment = "Prombūtnē no";
            $table->date('left_to')->nullable()->comment = "Prombūtnē līdz";
            $table->integer('left_reason_id')->nullable()->unsigned()->comment = "Prombūtnes iemesls";
            $table->integer('substit_empl_id')->nullable()->comment = "Aizvietotājs";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('dx_users');
            
            $table->index('substit_empl_id');
            $table->foreign('substit_empl_id')->references('id')->on('dx_users');
            
            $table->index('left_reason_id');
            $table->foreign('left_reason_id')->references('id')->on('in_left_reasons');
        });
        
        DB::unprepared("CREATE TRIGGER tr_dx_users_left_insert BEFORE INSERT ON  dx_users_left FOR EACH ROW 
            BEGIN
                
                IF (ifnull(NEW.left_from, now()) > ifnull(NEW.left_to, DATE_ADD(now(), INTERVAL 1 DAY))) THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Spēkā līdz datums nevar būt mazāks par Spēkā no datumu!';
                END IF;
  
                IF (now() between ifnull(NEW.left_from, DATE_ADD(now(), INTERVAL -1 DAY)) and ifnull(NEW.left_to, DATE_ADD(now(), INTERVAL 1 DAY))) THEN
                    UPDATE dx_users SET 
                        left_from = NEW.left_from,
                        left_to = NEW.left_to,
                        left_reason_id = NEW.left_reason_id,
                        substit_empl_id = NEW.substit_empl_id
                    WHERE
                        id = NEW.user_id;
                END IF;                
                
            END;
        ");
        
        DB::unprepared("CREATE TRIGGER tr_dx_users_left_update BEFORE UPDATE ON  dx_users_left FOR EACH ROW 
            BEGIN
                
                IF (ifnull(NEW.left_from, now()) > ifnull(NEW.left_to, DATE_ADD(now(), INTERVAL 1 DAY))) THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Spēkā līdz datums nevar būt mazāks par Spēkā no datumu!';
                END IF;
  
                IF (now() between ifnull(NEW.left_from, DATE_ADD(now(), INTERVAL -1 DAY)) and ifnull(NEW.left_to, DATE_ADD(now(), INTERVAL 1 DAY))) THEN
                    UPDATE dx_users SET 
                        left_from = NEW.left_from,
                        left_to = NEW.left_to,
                        left_reason_id = NEW.left_reason_id,
                        substit_empl_id = NEW.substit_empl_id
                    WHERE
                        id = NEW.user_id;
                END IF;                
                
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
        DB::unprepared('DROP TRIGGER IF EXISTS `tr_dx_users_left_insert`');
        Schema::dropIfExists('dx_users_left');
    }
}

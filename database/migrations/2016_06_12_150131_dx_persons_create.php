<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxPersonsCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {       
        Schema::create('dx_persons_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 200)->nullable()->comment = "Nosaukums";
            $table->string('code', 20)->nullable()->comment = "Apzīmējums";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->unique('code');
        });
        
        DB::table('dx_persons_types')->insert([
            ['title' => 'Sabiedrība ar ierobežotu atbildību', 'code' => 'SIA'],
            ['title' => 'Akciju sabiedrība', 'code' => 'AS'],
            ['title' => 'Valsts akciju sabiedrība', 'code' => 'VAS'],
            ['title' => 'Individuālais komersants', 'code' => 'IK'],
            ['title' => 'Zemnieku saimniecība', 'code' => 'z/s'],
        ]);
        
        Schema::create('dx_persons', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_legal_person')->nullable()->default(0)->comment = "Ir juridiska persona";
            $table->integer('person_type_id')->nullable()->unsigned()->comment = 'Darbības forma';
            $table->boolean('is_foreign')->nullable()->default(0)->comment = "Ir nerezidents";
            $table->boolean('is_vat')->nullable()->default(0)->comment = "Ir PVN maksātājs";
            $table->string('first_name', 100)->nullable()->comment = "Vārds";
            $table->string('last_name', 100)->nullable()->comment = "Uzvārds";
            $table->string('title', 500)->nullable()->comment = "Nosaukums";
            $table->string('search_title', 520)->nullable()->comment = "Nosaukums un reģ. nr.";
            $table->string('reg_nr', 20)->nullable()->comment = "Reģ. nr.";
            $table->string('vat_reg_nr', 20)->nullable()->comment = "PVN reģ. nr.";            
            $table->text('comments')->nullable()->comment = "Piezīmes";
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->unique('reg_nr');
            $table->unique('vat_reg_nr');
            $table->index('is_legal_person');
            $table->index('is_foreign');
            $table->index('is_vat');
            $table->index('search_title');
            $table->index('title');
            
            $table->index('person_type_id');
            $table->foreign('person_type_id')->references('id')->on('dx_persons_types');
        });
        
        DB::unprepared("CREATE TRIGGER tr_dx_persons_insert BEFORE INSERT ON  dx_persons FOR EACH ROW 
            BEGIN
                IF NEW.is_legal_person THEN
                    SET NEW.search_title = CONCAT(case when NEW.person_type_id is null then '' else CONCAT((select code from dx_persons_types where id=NEW.person_type_id), ' ') end, NEW.title, ' (', NEW.reg_nr, ')');
                    SET NEW.first_name = null;
                    SET NEW.last_name = null;
                ELSE
                    SET NEW.search_title = CONCAT(IFNULL(NEW.first_name,''), ' ', IFNULL(NEW.last_name,''), ' (', NEW.reg_nr, ')');
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
                    SET NEW.search_title = CONCAT(case when NEW.person_type_id is null then '' else CONCAT((select code from dx_persons_types where id=NEW.person_type_id), ' ') end, NEW.title, ' (', NEW.reg_nr, ')');
                    SET NEW.first_name = null;
                    SET NEW.last_name = null;
                ELSE
                    SET NEW.search_title = CONCAT(IFNULL(NEW.first_name,''), ' ', IFNULL(NEW.last_name,''), ' (', NEW.reg_nr, ')');
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
        DB::unprepared('DROP TRIGGER `tr_dx_persons_update`');
        DB::unprepared('DROP TRIGGER `tr_dx_persons_insert`');
        
        Schema::dropIfExists('dx_persons');
        Schema::dropIfExists('dx_persons_types');
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTriggerDxDocPersons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {  
        DB::unprepared('RENAME TABLE dx_doc_persons TO dx_doc;');
        DB::table('dx_objects')->where('db_name','=','dx_doc_persons')->update(['db_name'=>'dx_doc']);
        
        Schema::table('dx_doc_agreg', function (Blueprint $table) {
            $table->string('file_field_name', 100)->nullable()->comment = "Datnes lauka nosaukums";            
            $table->index('file_field_name');
        });
        
        Schema::table('dx_doc', function (Blueprint $table) {
            $table->integer('empl_creator_id')->unsigned()->nullable()->comment = "Sagatavotājs";            
            $table->integer('empl_signer_id')->unsigned()->nullable()->comment = "Parakstītājs";  
            
            $table->index('empl_creator_id');
            $table->foreign('empl_creator_id')->references('id')->on('in_employees');
            
            $table->index('empl_signer_id');
            $table->foreign('empl_signer_id')->references('id')->on('in_employees');
        });
        
        DB::unprepared("CREATE TRIGGER tr_dx_doc_insert AFTER INSERT ON  dx_doc FOR EACH ROW 
            BEGIN
                DECLARE rel_id INT;
                insert into dx_doc_agreg (list_id, item_id, reg_date, reg_nr, description, file_field_name, file_name, file_guid, source_id, person1_id, created_time, modified_time, created_user_id, modified_user_id) values (new.list_id, new.id, new.reg_date, new.reg_nr, new.about, 'file_name', new.file_name, new.file_guid, new.source_id, new.person1_id, new.created_time, new.modified_time, new.created_user_id, new.modified_user_id);               
            END;");
        
        DB::unprepared("CREATE TRIGGER tr_dx_doc_update AFTER UPDATE ON  dx_doc FOR EACH ROW 
            BEGIN               
                
                update dx_doc_agreg 
                set
                    list_id = new.list_id,
                    source_id = new.source_id,
                    reg_date = new.reg_date, 
                    description = new.about,
                    file_field_name = 'file_name',
                    file_name = new.file_name,
                    file_guid = new.file_guid,
                    person1_id = new.person1_id,
                    modified_time = new.modified_time,
                    modified_user_id = new.modified_user_id
                where
                    list_id = new.list_id AND
                    item_id = new.id
                ;
            END;");
        
        DB::unprepared("CREATE TRIGGER tr_dx_doc_delete AFTER DELETE ON  dx_doc FOR EACH ROW 
            BEGIN                
                delete from dx_doc_agreg where list_id = old.list_id AND item_id = old.id;
            END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        DB::unprepared('DROP TRIGGER `tr_dx_doc_insert`');
        
        DB::unprepared('DROP TRIGGER `tr_dx_doc_update`');
        
        DB::unprepared('DROP TRIGGER `tr_dx_doc_delete`');
        
        Schema::table('dx_doc_agreg', function (Blueprint $table) {           
            $table->dropColumn(['file_field_name']);
        });
        
        Schema::table('dx_doc', function (Blueprint $table) { 
            $table->dropForeign(['empl_creator_id']);
            $table->dropColumn(['empl_creator_id']);
            
            $table->dropForeign(['empl_signer_id']);
            $table->dropColumn(['empl_signer_id']);
        });
        
        DB::unprepared('RENAME TABLE dx_doc TO dx_doc_persons;');
        DB::table('dx_objects')->where('db_name','=','dx_doc')->update(['db_name'=>'dx_doc_persons']);
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPerformerToDxDoc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('dx_doc', function (Blueprint $table) {            
            
            $table->string('file_scan_name', 100)->nullable()->comment = "Ieskenētās datnes nosaukums";
            $table->string('file_scan_guid', 100)->nullable()->comment = "Ieskenētās datnes GUID";
            
            $table->integer('perform_empl_id')->nullable()->comment = "Izpildītājs";
            
            $table->index('perform_empl_id');
            $table->foreign('perform_empl_id')->references('id')->on('dx_users');
        });
        
        DB::unprepared('DROP TRIGGER IF EXISTS `tr_dx_doc_insert`');
        
        DB::unprepared('DROP TRIGGER If EXISTS `tr_dx_doc_update`');
        
        DB::unprepared("CREATE TRIGGER tr_dx_doc_insert AFTER INSERT ON  dx_doc FOR EACH ROW 
            BEGIN
                DECLARE rel_id INT;
                insert into dx_doc_agreg (
                    list_id, 
                    item_id, 
                    reg_date, 
                    reg_nr, 
                    description, 
                    file_field_name, 
                    file_name, 	
                    file_text, 
                    file_guid, 
                    source_id, 
                    person1_id, 
                    created_time, 
                    modified_time, 
                    created_user_id, 
                    modified_user_id
                ) values (
                    new.list_id, 
                    new.id, 
                    new.reg_date, 
                    new.reg_nr, 
                    new.about, 
                    case when new.file_scan_guid is null then 'file_name' else 'file_scan_name' end, 
                    ifnull(new.file_scan_name, new.file_name), 
                    new.file_dx_text, 
                    ifnull(new.file_scan_guid, new.file_guid), 
                    new.source_id, 
                    new.person1_id, 
                    new.created_time, 
                    new.modified_time, 
                    new.created_user_id, 
                    new.modified_user_id);               
            END;");
        
        DB::unprepared("CREATE TRIGGER tr_dx_doc_update AFTER UPDATE ON  dx_doc FOR EACH ROW 
            BEGIN               
                
                update dx_doc_agreg 
                set
                    list_id = new.list_id,
                    source_id = new.source_id,
                    reg_date = new.reg_date, 
                    description = new.about,
                    file_field_name = case when new.file_scan_guid is null then 'file_name' else 'file_scan_name' end,
                    file_name = ifnull(new.file_scan_name, new.file_name),
                    file_text = new.file_dx_text,
                    file_guid = ifnull(new.file_scan_guid, new.file_guid),
                    person1_id = new.person1_id,
                    modified_time = new.modified_time,
                    modified_user_id = new.modified_user_id
                where
                    list_id = new.list_id AND
                    item_id = new.id
                ;
            END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {        
        DB::unprepared('DROP TRIGGER IF EXISTS `tr_dx_doc_insert`');
        
        DB::unprepared('DROP TRIGGER If EXISTS `tr_dx_doc_update`');
        
        Schema::table('dx_doc', function (Blueprint $table) {
            $table->dropForeign(['perform_empl_id']);
            $table->dropColumn(['perform_empl_id']);
            
            $table->dropColumn(['file_scan_name']);
            $table->dropColumn(['file_scan_guid']);
        });
    }
}

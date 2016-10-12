<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateClientNrTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `tr_dx_doc_insert`');
         
        DB::unprepared("CREATE TRIGGER tr_dx_doc_insert AFTER INSERT ON  dx_doc FOR EACH ROW 
            BEGIN
                DECLARE rel_id INT;
                insert into dx_doc_agreg (
                    list_id, 
                    item_id,
                    kind_id,
                    reg_nr_client,
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
                    new.doc_type_id,
                    new.reg_nr_client,
                    ifnull(new.reg_date, new.modified_time), 
                    ifnull(new.reg_nr, new.id),
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
        
        DB::unprepared('DROP TRIGGER IF EXISTS `tr_dx_doc_update`');
         
        DB::unprepared("CREATE TRIGGER tr_dx_doc_update AFTER UPDATE ON  dx_doc FOR EACH ROW 
            BEGIN               
                
                update dx_doc_agreg 
                set
                    list_id = new.list_id,
                    source_id = new.source_id,
                    kind_id = new.doc_type_id,
                    reg_date = ifnull(new.reg_date, new.modified_time),
                    reg_nr = ifnull(new.reg_nr, new.id),
                    reg_nr_client = new.reg_nr_client,
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
        
        $docs = DB::table('dx_doc')->whereNotNull('reg_nr_client')->get();
        
        foreach($docs as $doc) {
            DB::table('dx_doc_agreg')
            ->where('item_id', '=', $doc->id)
            ->update(['reg_nr_client' => $doc->reg_nr_client]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `tr_dx_doc_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `tr_dx_doc_update`');
        
    }
}

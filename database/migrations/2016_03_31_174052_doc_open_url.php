<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DocOpenUrl extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_documents_lotus', function (Blueprint $table) {
            $table->string('doc_url', 1000)->nullable()->comment = "Dokumenta URL";
        });
        
        $fld_id = DB::table('dx_lists_fields')->insertGetId(
            ['list_id' => 229, 'db_name' => 'doc_url', 'type_id' => 1, 'max_lenght' => 1000,  'title_list' => 'Dokumenta URL', 'title_form' => 'Dokumenta URL', 'hint' => 'Jānorāda pilnais URL un noteiktā vietā jāieliek [UNID], kurā sistēma pados unid vērtību. Piemēram, http://www.mylotus.com?unid=[UNID]&param=val']
        );
        
        $form_row = DB::table('dx_forms')->where('list_id', '=', 229)->first();
        
        DB::table('dx_forms_fields')->insert(['list_id'=>229, 'form_id'=>$form_row->id, 'field_id'=>$fld_id,'order_index'=>35]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_documents_lotus', function (Blueprint $table) {
            $table->dropColumn(['doc_url']);
        });
        
        $form_row = DB::table('dx_forms')->where('list_id', '=', 229)->first();
        $fld_row = DB::table('dx_lists_fields')->where('list_id', '=', 229)->where('db_name', '=', 'doc_url')->first();
        
        DB::table('dx_forms_fields')->where('list_id', '=', 229)->where('form_id', '=', $form_row->id)->where('field_id', '=', $fld_row->id)->delete();
        DB::table('dx_lists_fields')->where('id', '=', $fld_row->id)->delete();
    }
}

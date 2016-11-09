<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class MakeRegistersUserDocs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'in_personal_docs', 'title' => 'User document types' , 'is_history_logic' => 1]);

        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = "User document types";
        $list_gen->form_title = "User document type";
        $list_gen->doMethod();
        
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'in_employees_personal_docs', 'title' => 'User documents' , 'is_history_logic' => 1]);

        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = "User documents";
        $list_gen->form_title = "User document";
        $list_gen->doMethod();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $list = App\Libraries\DBHelper::getListByTable('in_personal_docs');

        $list_del = new Structure\StructMethod_register_delete();
        $list_del->list_id = $list->id;
        $list_del->doMethod(); 
        
        $list = App\Libraries\DBHelper::getListByTable('in_employees_personal_docs');

        $list_del = new Structure\StructMethod_register_delete();
        $list_del->list_id = $list->id;
        $list_del->doMethod(); 
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduProgrammsAvailUi extends EduMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function edu_up()
    {       
        
        DB::transaction(function () {
            $table_name = "edu_programms_avail";
            $list_name = trans('db_edu_programms_avail.list_name');
            $item_name = trans('db_edu_programms_avail.item_name');

             // create register
            $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $table_name, 'title' => $list_name , 'is_history_logic' => 1]);
            $list_gen = new Structure\StructMethod_register_generate();
            $list_gen->obj_id = $obj_id;
            $list_gen->register_title = $list_name;
            $list_gen->form_title = $item_name;
            $list_gen->doMethod();

            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;       

            // reorganize view fields - hide or remove unneeded
            \App\Libraries\DBHelper::removeFieldsFromAllViews($table_name, ['id'], true); // hide ID field                       
                        
            // user rights
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1, 'is_import_rights' => 1, 'is_view_rights' => 1]); // Sys admins
            
            $parent_menu = DB::table('dx_menu')->where('title', '=', trans('db_dx_menu.lbl_classifiers'))->first();
            $arr_params = [
                'menu_list_id' => $list_id, 
                'list_title' => $list_name,
                'menu_parent_id' => $parent_menu->id
            ];
            App\Libraries\DBHelper::makeMenu($arr_params);
            
            DB::table('edu_programms_avail')->insert(trans('db_edu_programms_avail.values'));
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function edu_down()
    {        
        DB::transaction(function () {
            \App\Libraries\DBHelper::deleteRegister('edu_programms_avail');
            DB::table('dx_objects')->where('db_name', '=', 'edu_programms_avail')->delete();
        });
    }
}

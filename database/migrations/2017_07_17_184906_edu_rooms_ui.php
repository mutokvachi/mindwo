<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduRoomsUi extends EduMigration
{
    private $table_name = "edu_rooms";
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function edu_up()
    {       
        
        DB::transaction(function () {
            
            $list_name = trans('db_' . $this->table_name . '.list_name');
            $item_name = trans('db_' . $this->table_name . '.item_name');

             // create register
            $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $this->table_name, 'title' => $list_name , 'is_history_logic' => 1]);
            $list_gen = new Structure\StructMethod_register_generate();
            $list_gen->obj_id = $obj_id;
            $list_gen->register_title = $list_name;
            $list_gen->form_title = $item_name;
            $list_gen->doMethod();

            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id;       

            // reorganize view fields - hide or remove unneeded
            \App\Libraries\DBHelper::removeFieldsFromAllViews($this->table_name, ['id', 'title'], true); // hide ID field                       
            \App\Libraries\DBHelper::removeFieldsFromAllForms($this->table_name, ['id', 'title'], false);
             
            // user rights
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1, 'is_import_rights' => 1, 'is_view_rights' => 1]); // Sys admins
            
            $parent_menu = DB::table('dx_menu')->where('title', '=', trans('db_dx_menu.lbl_classifiers'))->first();
            $arr_params = [
                'menu_list_id' => $list_id, 
                'list_title' => $list_name,
                'menu_parent_id' => $parent_menu->id
            ];
            App\Libraries\DBHelper::makeMenu($arr_params);
            
            // add tab to org
            // Add tab to subject group day
            $org_list = \App\Libraries\DBHelper::getListByTable("edu_orgs");
            $form = DB::table('dx_forms')->where('list_id', '=', $org_list->id)->first();
            $org_field = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'org_id')->first();
            
            $tab_main_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_edu_orgs.tab_rooms'),
                'is_custom_data' => 0,
                'order_index' => 5,
                'grid_list_id' => $list_id,
                'grid_list_field_id' => $org_field->id
            ]);
            
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'is_elearn')
                    ->update([
                        'hint' =>trans('db_edu_rooms.is_elearn_hint')
                    ]);
            
            App\Libraries\DBHelper::updateFormField($list_id, "is_elearn", ['row_type_id' => 3]);
            App\Libraries\DBHelper::updateFormField($list_id, "room_limit", ['row_type_id' => 3]);
            App\Libraries\DBHelper::updateFormField($list_id, "is_computers", ['row_type_id' => 3]);
            
            App\Libraries\DBHelper::updateFormField($list_id, "org_id", ['row_type_id' => 2]);
            App\Libraries\DBHelper::updateFormField($list_id, "room_nr", ['row_type_id' => 2]);
            
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
            \App\Libraries\DBHelper::deleteRegister($this->table_name);
            DB::table('dx_objects')->where('db_name', '=', $this->table_name)->delete();
        });
    }
}

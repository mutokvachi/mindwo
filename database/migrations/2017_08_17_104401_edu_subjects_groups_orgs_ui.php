<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduSubjectsGroupsOrgsUi extends EduMigration
{
    private $table_name = "edu_subjects_groups_orgs";
    
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
            \App\Libraries\DBHelper::removeFieldsFromAllViews($this->table_name, ['id'], true); // hide ID field                       
            \App\Libraries\DBHelper::removeFieldsFromAllForms($this->table_name, ['id'], false);
            
            // user rights
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1, 'is_import_rights' => 1, 'is_view_rights' => 1]); // Sys admins  
            DB::table('dx_roles_lists')->insert(['role_id' => 74, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1, 'is_import_rights' => 0, 'is_view_rights' => 0]); // Main coordinators            
                       
            // Add tab to gorups
            $group_list = \App\Libraries\DBHelper::getListByTable("edu_subjects_groups");
            $form = DB::table('dx_forms')->where('list_id', '=', $group_list->id)->first();
            $group_field = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'group_id')->first();
            
            $tab_main_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_edu_subjects_groups.tab_orgs'),
                'is_custom_data' => 0,
                'order_index' => 25,
                'grid_list_id' => $list_id,
                'grid_list_field_id' => $group_field->id
            ]);
            
            $form = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
            $act_id = DB::table('dx_actions')->insertGetId([
                'title' => trans('db_' . $this->table_name . '.act_validate_org_quota'),
                'code' => 'VALIDATE_ORG_QUOTA'
            ]);
            
            DB::table('dx_forms_actions')->insert([
                'form_id' => $form->id,
                'action_id' => $act_id
            ]);
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
            DB::table('dx_actions')->where('code', '=', 'VALIDATE_ORG_QUOTA')->delete();
        });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduSubjectsGroupsUi extends EduMigration
{
   private $table_name = "edu_subjects_groups";
    
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
            \App\Libraries\DBHelper::removeFieldsFromAllForms($this->table_name, ['id', 'title'], false);
            
            // user rights
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1, 'is_import_rights' => 1, 'is_view_rights' => 1]); // Sys admins
            
            // menu
            $parent_menu = DB::table('dx_menu')->where('title', '=', trans('db_dx_menu.lbl_edu_learning'))->first();
            $arr_params = [
                'menu_list_id' => $list_id, 
                'list_title' => trans('db_' . $this->table_name . '.list_name'),
                'menu_parent_id' => $parent_menu->id,
            ];
            App\Libraries\DBHelper::makeMenu($arr_params);
                        
            App\Libraries\DBHelper::removeFieldsFromAllViews($list_id, [
                'is_generated',
                'is_inner_group',
                'inner_org_id',
                'canceled_time',
                'canceled_reason',
                'approved_time',
            ], false);
            
            App\Libraries\DBHelper::removeFieldsFromAllViews($list_id, ['title'], true);
            
            // Adjust fields
            $form = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
            
            $tab_main_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_' . $this->table_name . '.tab_main'),
                'is_custom_data' => 1,
                'order_index' => 10
            ]);
            
            App\Libraries\DBHelper::updateFormField($list_id, "seats_limit", ['row_type_id' => 3, 'tab_id' => $tab_main_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "signup_due", ['row_type_id' => 3, 'tab_id' => $tab_main_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "is_published", ['row_type_id' => 3, 'tab_id' => $tab_main_id]);
            
            App\Libraries\DBHelper::updateFormField($list_id, "is_generated", ['row_type_id' => 3, 'tab_id' => $tab_main_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "approved_time", ['row_type_id' => 3, 'tab_id' => $tab_main_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "is_inner_group", ['row_type_id' => 3, 'tab_id' => $tab_main_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "inner_org_id", ['tab_id' => $tab_main_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "canceled_time", ['row_type_id' => 2, 'tab_id' => $tab_main_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "canceled_reason", ['row_type_id' => 2, 'tab_id' => $tab_main_id]);
                        
            // create view for using in related lookups
            $view_id = DB::table('dx_views')->insertGetId([
                'list_id' => $list_id,
                'title' => trans('db_edu_subjects_groups.view_related'),
                'view_type_id' => 1,
                'is_hidden_from_main_grid' => 1,
                'is_hidden_from_tabs' => 1,
                'is_for_lookup' => 1,
            ]);

            DB::table('dx_views_fields')->insert([
               'list_id' => $list_id,
               'view_id' => $view_id,
               'field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'id')->first()->id,
            ]);

            DB::table('dx_views_fields')->insert([
               'list_id' => $list_id,
               'view_id' => $view_id,
               'field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'title')->first()->id,
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
        });
    }
}

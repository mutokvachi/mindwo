<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduProgrammsUi extends EduMigration
{
    private $table_name = "edu_programms";
    
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
            
            // create parent menu
            $arr_params = [
                'menu_list_id' => null, 
                'list_title' => trans('db_dx_menu.lbl_edu_learning'),
                'menu_parent_id' => null,
                'menu_order_index' => 30,
                'menu_icon' => 'fa fa-graduation-cap',
            ];
            $parent_menu_id = App\Libraries\DBHelper::makeMenu($arr_params);
            
            $arr_params = [
                'menu_list_id' => $list_id, 
                'list_title' => $list_name,
                'menu_parent_id' => $parent_menu_id
            ];
            App\Libraries\DBHelper::makeMenu($arr_params);
            
            // Adjust fields
            $form = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
            
            $tab_main_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_' . $this->table_name . '.tab_main'),
                'is_custom_data' => 1,
                'order_index' => 20
            ]);
            
            $tab_descr_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_' . $this->table_name . '.tab_descr'),
                'is_custom_data' => 1,
                'order_index' => 20
            ]);
            
            $tab_tests_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_' . $this->table_name . '.tab_tests'),
                'is_custom_data' => 1,
                'order_index' => 30
            ]);
            
            App\Libraries\DBHelper::updateFormField($list_id, "sub_title", ['tab_id' => $tab_main_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "parent_id", ['tab_id' => $tab_main_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "avail_id", ['tab_id' => $tab_main_id]);            
            App\Libraries\DBHelper::updateFormField($list_id, "icon_id", ['tab_id' => $tab_main_id, 'row_type_id' => 2]);
            App\Libraries\DBHelper::updateFormField($list_id, "is_published", ['tab_id' => $tab_main_id, 'row_type_id' => 2]);
            
            App\Libraries\DBHelper::updateFormField($list_id, "description", ['tab_id' => $tab_descr_id]);
            
            App\Libraries\DBHelper::updateFormField($list_id, "needs_survey_id", ['tab_id' => $tab_tests_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "qualify_test_id", ['tab_id' => $tab_tests_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "cert_numerator_id", ['tab_id' => $tab_tests_id]);
            
            $parent_id = DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'parent_id')
                    ->first()->id;
            
            DB::table('dx_lists_fields')
                    ->where('id', '=', $parent_id)
                    ->update([
                        'type_id' => App\Libraries\DBHelper::FIELD_TYPE_MULTILEVEL,
                        'rel_parent_field_id' => $parent_id
                    ]);
            
            App\Libraries\DBHelper::removeFieldsFromAllViews($list_id, [
                'sub_title',
                'avail_id',
                'icon_id',
                'description',
                'needs_survey_id',
                'qualify_test_id',
                'cert_numerator_id',
            ], false);
            
            
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
            
            DB::table('dx_menu')
                    ->where('title', '=', trans('db_dx_menu.lbl_edu_learning'))
                    ->whereNull('parent_id')
                    ->delete();
        });
    }
}

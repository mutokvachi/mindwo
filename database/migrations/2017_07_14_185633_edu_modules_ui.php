<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduModulesUi extends EduMigration
{
    private $table_name = "edu_modules";
    
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
            \App\Libraries\DBHelper::removeFieldsFromAllViews($this->table_name, ['id', 'title_full'], true); // hide ID field                       
            \App\Libraries\DBHelper::removeFieldsFromAllForms($this->table_name, ['id', 'title_full'], false);
            
            // user rights
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1, 'is_import_rights' => 1, 'is_view_rights' => 1]); // Sys admins
            
            // create menu
            $parent_menu = DB::table('dx_menu')->where('title', '=', trans('db_dx_menu.lbl_edu_learning'))->first();            
            $arr_params = [
                'menu_list_id' => $list_id, 
                'list_title' => $list_name,
                'menu_parent_id' => $parent_menu->id
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
            
            App\Libraries\DBHelper::updateFormField($list_id, "programm_id", ['tab_id' => $tab_main_id, 'row_type_id' => 2]);
            App\Libraries\DBHelper::updateFormField($list_id, "code", ['tab_id' => $tab_main_id, 'row_type_id' => 2]);
                                    
            App\Libraries\DBHelper::updateFormField($list_id, "icon_id", ['tab_id' => $tab_main_id, 'row_type_id' => 2]);
            App\Libraries\DBHelper::updateFormField($list_id, "is_published", ['tab_id' => $tab_main_id, 'row_type_id' => 2]);
            
            App\Libraries\DBHelper::updateFormField($list_id, "description", ['tab_id' => $tab_descr_id]);            
            
            App\Libraries\DBHelper::updateFormField($list_id, "qualify_test_id", ['tab_id' => $tab_tests_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "cert_numerator_id", ['tab_id' => $tab_tests_id, 'row_type_id' => 2]);
            App\Libraries\DBHelper::updateFormField($list_id, "subj_quota_percent", ['tab_id' => $tab_tests_id, 'row_type_id' => 2]);
            
            App\Libraries\DBHelper::removeFieldsFromAllViews($list_id, [
                'icon_id',
                'description',                
                'qualify_test_id',
                'cert_numerator_id',
            ], false);
            
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'subj_quota_percent')
                    ->update([
                'hint' => trans('db_' . $this->table_name . '.subj_quota_percent_hint')
            ]);
            
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'icon_id')
                    ->update([
                'hint' => trans('db_' . $this->table_name . '.icon_id_hint')
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

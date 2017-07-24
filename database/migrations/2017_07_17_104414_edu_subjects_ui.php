<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduSubjectsUi extends EduMigration
{
    private $table_name = "edu_subjects";
    
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
            
            // menu
            $parent_menu = DB::table('dx_menu')->where('title', '=', trans('db_dx_menu.lbl_edu_learning'))->first();
            $arr_params = [
                'menu_list_id' => $list_id, 
                'list_title' => trans('db_' . $this->table_name . '.list_name'),
                'menu_parent_id' => $parent_menu->id,
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
            
            App\Libraries\DBHelper::updateFormField($list_id, "subject_type_id", ['row_type_id' => 2]);
            App\Libraries\DBHelper::updateFormField($list_id, "avail_id", ['row_type_id' => 2]);
            
            App\Libraries\DBHelper::updateFormField($list_id, "programm_id", ['tab_id' => $tab_main_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "subject_code", ['tab_id' => $tab_main_id, 'row_type_id' => 2]);
            App\Libraries\DBHelper::updateFormField($list_id, "project_code", ['tab_id' => $tab_main_id, 'row_type_id' => 2]);            
            App\Libraries\DBHelper::updateFormField($list_id, "credit_points", ['tab_id' => $tab_main_id, 'row_type_id' => 3]);
            App\Libraries\DBHelper::updateFormField($list_id, "is_org_approve_need", ['tab_id' => $tab_main_id, 'row_type_id' => 3]);
            App\Libraries\DBHelper::updateFormField($list_id, "is_published", ['tab_id' => $tab_main_id, 'row_type_id' => 3]);
            
            App\Libraries\DBHelper::updateFormField($list_id, "is_fee", ['tab_id' => $tab_main_id, 'row_type_id' => 3, 'group_label' => trans('db_' . $this->table_name . '.group_costs')]);
            App\Libraries\DBHelper::updateFormField($list_id, "price_for_teacher", ['tab_id' => $tab_main_id, 'row_type_id' => 3]);
            App\Libraries\DBHelper::updateFormField($list_id, "price_for_rooms", ['tab_id' => $tab_main_id, 'row_type_id' => 3]);
                        
            App\Libraries\DBHelper::updateFormField($list_id, "description", ['tab_id' => $tab_descr_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "learning_url", ['tab_id' => $tab_descr_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "user_approval_msg", ['tab_id' => $tab_descr_id]);
            
            App\Libraries\DBHelper::updateFormField($list_id, "info_survey_id", ['tab_id' => $tab_tests_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "subject_pretest_id", ['tab_id' => $tab_tests_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "is_subj_qual_test_ok_need", ['tab_id' => $tab_tests_id, 'row_type_id' => 3]);
            App\Libraries\DBHelper::updateFormField($list_id, "is_progr_qual_test_ok_need", ['tab_id' => $tab_tests_id, 'row_type_id' => 3]);
            App\Libraries\DBHelper::updateFormField($list_id, "is_subj_qual_test_ok_need", ['tab_id' => $tab_tests_id, 'row_type_id' => 3]);
            App\Libraries\DBHelper::updateFormField($list_id, "cert_numerator_id", ['tab_id' => $tab_tests_id]);
            
            $prorgamm_id = DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'programm_id')
                    ->first()->id;
            
            $programms_list = \App\Libraries\DBHelper::getListByTable('edu_programms');
            
            $programms_parent_id = DB::table('dx_lists_fields')
                    ->where('list_id', '=', $programms_list->id)
                    ->where('db_name', '=', 'parent_id')
                    ->first()->id;
            
            DB::table('dx_lists_fields')
                    ->where('id', '=', $prorgamm_id)
                    ->update([
                        'type_id' => App\Libraries\DBHelper::FIELD_TYPE_MULTILEVEL,
                        'rel_parent_field_id' => $programms_parent_id
                    ]);            
            
            App\Libraries\DBHelper::removeFieldsFromAllViews($list_id, [
                'subject_code',
                'project_code',
                'credit_points',
                'is_org_approve_need',
                'is_fee',
                'price_for_teacher',
                'price_for_rooms',
                'learning_url',
                'description',
                'user_approval_msg',
                'info_survey_id',
                'subject_pretest_id',
                'is_subj_qual_test_ok_need',
                'is_progr_qual_test_ok_need',
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
        });
    }
}

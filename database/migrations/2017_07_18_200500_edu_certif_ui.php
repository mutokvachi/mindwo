<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduCertifUi extends EduMigration
{
    private $table_name = "edu_certif";
    
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
                        
            App\Libraries\DBHelper::removeFieldsFromAllViews($list_id, [
                'content'
            ], false);
            
            // fix students related grid ID
            $student_list = DB::table('dx_lists')->where('list_title', '=', trans('db_dx_users.list_title_student'))->first();            
            $student_display = DB::table('dx_lists_fields')->where('list_id', '=', $student_list->id)->where('db_name', '=', 'full_name_code')->first();
            
            DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'user_id')->update([
                'rel_list_id' => $student_list->id,
                'rel_display_field_id' => $student_display->id
            ]);
            
            App\Libraries\DBHelper::updateFormField($list_id, "reg_nr", ['row_type_id' => 2]);
            App\Libraries\DBHelper::updateFormField($list_id, "reg_date", ['row_type_id' => 2]);
            
            // Make default view not visible in sub-grids
            DB::table('dx_views')->where('list_id', '=', $list_id)->update([
                'is_hidden_from_tabs' => 1
            ]);
            
            // add tab to user profile  
            $profile_list = DB::table('dx_lists')->where('list_title', '=', trans('db_dx_users.list_title_profile'))->first();            
            $form = DB::table('dx_forms')->where('list_id', '=', $profile_list->id)->first();
            $subj_field = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'user_id')->first();
            
            $tab_main_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_dx_users.tab_certif'),
                'is_custom_data' => 0,
                'order_index' => 30,
                'grid_list_id' => $list_id,
                'grid_list_field_id' => $subj_field->id
            ]);
            
            // create view for sub-grids
            $view_id = DB::table('dx_views')->insertGetId([
                'list_id' => $list_id,
                'title' => trans('db_dx_users.tab_certif'),
                'view_type_id' => 1,
                'is_hidden_from_main_grid' => 1,
                'is_hidden_from_tabs' => 0,
            ]);

            
            DB::table('dx_views_fields')->insert([
               'list_id' => $list_id,
               'view_id' => $view_id,
               'field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'id')->first()->id,
               'is_hidden' => 1,
            ]);
            
            DB::table('dx_views_fields')->insert([
               'list_id' => $list_id,
               'view_id' => $view_id,
               'field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'user_id')->first()->id,
               'is_hidden' => 1,
            ]);

            DB::table('dx_views_fields')->insert([
               'list_id' => $list_id,
               'view_id' => $view_id,
               'field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'reg_date')->first()->id,
               'alias_name' => trans('db_dx_users.cert_date'),
               'is_item_link' => 1,
               'order_index' => 10,
            ]);
            
            DB::table('dx_views_fields')->insert([
               'list_id' => $list_id,
               'view_id' => $view_id,
               'field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'subject_id')->first()->id,
               'order_index' => 20,
            ]);
            
            DB::table('dx_views_fields')->insert([
               'list_id' => $list_id,
               'view_id' => $view_id,
               'field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'file_name')->first()->id,
               'alias_name' => trans('db_dx_users.cert_file'),
               'order_index' => 30,
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

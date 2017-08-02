<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduMaterialsUi extends EduMigration
{
    private $table_name = "edu_materials";
    
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
                'description',
                'is_embeded',
                'embeded',
                'author',
                'org_id',
                'is_public_access',
            ], false);
            
            // Adjust fields
            $form = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
            
            $tab_main_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_' . $this->table_name . '.tab_main'),
                'is_custom_data' => 1,
                'order_index' => 10
            ]);
            
            $tab_desc_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_' . $this->table_name . '.tab_description'),
                'is_custom_data' => 1,
                'order_index' => 20
            ]);
            
            $tab_access_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_' . $this->table_name . '.tab_access'),
                'is_custom_data' => 1,
                'order_index' => 25
            ]);
            
            App\Libraries\DBHelper::updateFormField($list_id, "description", ['tab_id' => $tab_desc_id]);
            
            App\Libraries\DBHelper::updateFormField($list_id, "file_name", ['tab_id' => $tab_main_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "author", ['row_type_id' => 3, 'tab_id' => $tab_main_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "is_published", ['row_type_id' => 3, 'tab_id' => $tab_main_id]);
                        
            App\Libraries\DBHelper::updateFormField($list_id, "org_id", ['tab_id' => $tab_access_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "teacher_id", ['row_type_id' => 2, 'tab_id' => $tab_access_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "is_public_access", ['row_type_id' => 2, 'tab_id' => $tab_access_id]);
            
            // fix teacher related grid ID
            $teacher_list = DB::table('dx_lists')->where('list_title', '=', trans('db_dx_users.list_title_teacher'))->first();            
            $teacher_display = DB::table('dx_lists_fields')->where('list_id', '=', $teacher_list->id)->where('db_name', '=', 'display_name')->first();
            
            DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'teacher_id')->update([
                'rel_list_id' => $teacher_list->id,
                'rel_display_field_id' => $teacher_display->id
            ]);
            
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'teacher_id')
                    ->update([
                        'hint' => trans('db_' . $this->table_name . '.teacher_id_hint')
                    ]);
            
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'org_id')
                    ->update([
                        'hint' => trans('db_' . $this->table_name . '.org_id_hint')
                    ]);
            
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'is_public_access')
                    ->update([
                        'hint' => trans('db_' . $this->table_name . '.is_public_access_hint')
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

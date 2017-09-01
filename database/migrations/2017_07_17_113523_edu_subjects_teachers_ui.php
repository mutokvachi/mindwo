<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduSubjectsTeachersUi extends EduMigration
{
    private $table_name = "edu_subjects_teachers";
    
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
           
            // fix teacher related grid ID
            $teacher_list = DB::table('dx_lists')->where('list_title', '=', trans('db_dx_users.list_title_teacher'))->first();            
            $teacher_display = DB::table('dx_lists_fields')->where('list_id', '=', $teacher_list->id)->where('db_name', '=', 'full_name_code')->first();
            
            DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'teacher_id')->update([
                'rel_list_id' => $teacher_list->id,
                'rel_display_field_id' => $teacher_display->id
            ]);
            
            // Add tab to subjects
            $subj_list = \App\Libraries\DBHelper::getListByTable("edu_subjects");
            $form = DB::table('dx_forms')->where('list_id', '=', $subj_list->id)->first();
            $subj_field = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'subject_id')->first();
            
            $tab_main_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_edu_subjects.tab_teachers'),
                'is_custom_data' => 0,
                'order_index' => 40,
                'grid_list_id' => $list_id,
                'grid_list_field_id' => $subj_field->id
            ]);
            
            // Add tab to teachers
            $subj_list = DB::table('dx_lists')->where('list_title', '=', trans('db_dx_users.list_title_teacher'))->first();
            $form = DB::table('dx_forms')->where('list_id', '=', $subj_list->id)->first();
            $subj_field = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'teacher_id')->first();
            
            $tab_main_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_dx_users.tab_subjects'),
                'is_custom_data' => 0,
                'order_index' => 20,
                'grid_list_id' => $list_id,
                'grid_list_field_id' => $subj_field->id
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

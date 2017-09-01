<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduSubjectsGroupsAddMainTeacherUi extends EduMigration
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
            
            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id;       

            $teacher_list = DB::table('dx_lists')
                        ->where('list_title', '=', trans('db_dx_users.list_title_teacher'))
                        ->first();
            
            $teacher_fld = DB::table('dx_lists_fields')
                       ->where('list_id', '=', $teacher_list->id)
                       ->where('db_name', '=', 'full_name_code')
                       ->first();
            
            $frm = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
            $tab = DB::table('dx_forms_tabs')->where('form_id', '=', $frm->id)->where('title', '=', trans('db_' . $this->table_name . '.tab_main'))->first();

            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'main_teacher_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_RELATED,
                'title_list' => trans('db_' . $this->table_name . '.main_teacher_id'),
                'title_form' => trans('db_' . $this->table_name . '.main_teacher_id'),
                'rel_list_id' => $teacher_list->id,
                'rel_display_field_id' => $teacher_fld->id,
                'hint' => trans('db_' . $this->table_name . '.main_teacher_id_hint')
            ]);
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['tab_id' => $tab->id]); 
            
            App\Libraries\DBHelper::reorderFormField($list_id, 'main_teacher_id', 'is_published');
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
            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id; 
            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'main_teacher_id');           
        });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduSubjectsAddHoursUi extends EduMigration
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
            
            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id;       
            
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'academic_hours',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_INT,
                'title_list' => trans('db_' . $this->table_name . '.academic_hours'),
                'title_form' => trans('db_' . $this->table_name . '.academic_hours')
            ]);

            $frm = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
            $tab = DB::table('dx_forms_tabs')->where('form_id', '=', $frm->id)->where('title', '=', trans('db_' . $this->table_name . '.tab_main'))->first();

            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['tab_id' => $tab->id, 'row_type_id' => 3]); 
            
            App\Libraries\DBHelper::reorderFormField($list_id, 'academic_hours', 'coordinator_id');
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
            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'academic_hours');           
        });
    }
}

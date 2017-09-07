<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\HRMigration;

class DxSalariesAddProbationUi extends HRMigration
{
    private $table_name = "dx_users_salaries";
    
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
                'db_name' => 'probation_salary',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => trans('db_' . $this->table_name . '.probation_salary'),
                'title_form' => trans('db_' . $this->table_name . '.probation_salary'),
                'hint' => trans('db_' . $this->table_name . '.probation_salary_hint')
            ]);
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id); 
            
            App\Libraries\DBHelper::reorderFormField($list_id, 'probation_salary', 'annual_salary');

            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'probation_months',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_INT,
                'title_list' => trans('db_' . $this->table_name . '.probation_months'),
                'title_form' => trans('db_' . $this->table_name . '.probation_months'),
                'hint' => trans('db_' . $this->table_name . '.probation_months_hint')
            ]);
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id); 
            
            App\Libraries\DBHelper::reorderFormField($list_id, 'probation_months', 'probation_salary');
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
            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'probation_salary'); 
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'probation_months');          
        });
    }
}

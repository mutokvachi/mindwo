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
    public function hr_up()
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
                'hint' => trans('db_' . $this->table_name . '.probation_salary_hint'),
                'is_crypted' => 1,
                'max_lenght' => 200
            ]);
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['row_type_id' => 3]); 

            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'probation_salary_annual',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => trans('db_' . $this->table_name . '.probation_salary_annual'),
                'title_form' => trans('db_' . $this->table_name . '.probation_salary_annual'),
                'is_crypted' => 1,
                'max_lenght' => 200
            ]);
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['row_type_id' => 3, 'is_readonly' => 1]);
            
            App\Libraries\DBHelper::updateFormField($list_id, "salary_type_id", ['row_type_id' => 2]);
            App\Libraries\DBHelper::updateFormField($list_id, "currency_id", ['row_type_id' => 2]);
            App\Libraries\DBHelper::reorderFormField($list_id, 'currency_id', 'salary_type_id');
            
            App\Libraries\DBHelper::updateFormField($list_id, "salary", ['row_type_id' => 2]);
            App\Libraries\DBHelper::updateFormField($list_id, "annual_salary", ['row_type_id' => 2]);
            App\Libraries\DBHelper::reorderFormField($list_id, 'annual_salary', 'salary');

            App\Libraries\DBHelper::reorderFormField($list_id, 'probation_salary', 'annual_salary');

            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'probation_months',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_INT,
                'title_list' => trans('db_' . $this->table_name . '.probation_months'),
                'title_form' => trans('db_' . $this->table_name . '.probation_months'),
                'hint' => trans('db_' . $this->table_name . '.probation_months_hint')
            ]);
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['row_type_id' => 3]); 
            
            App\Libraries\DBHelper::reorderFormField($list_id, 'probation_months', 'probation_salary');
            App\Libraries\DBHelper::reorderFormField($list_id, 'probation_salary_annual', 'probation_months');

            $frm = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();

            DB::table('dx_forms_js')->where('form_id', '=', $frm->id)->delete();

            \App\Libraries\DBHelper::addJavaScriptToForm($list_id, '2017_09_07_dx_users_salaries.js', trans('db_' . $this->table_name . '.form_js'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function hr_down()
    {        
        DB::transaction(function () {            
            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id; 
            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'probation_salary'); 
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'probation_months');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'probation_salary_annual');   
            
            $frm = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
            
            DB::table('dx_forms_js')->where('form_id', '=', $frm->id)->delete();
            
            \App\Libraries\DBHelper::addJavaScriptToForm($list_id, '2017_01_27_dx_users_salaries.js', trans('db_' . $this->table_name . '.form_js'));
        });
    }
}

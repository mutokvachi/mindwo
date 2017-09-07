<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\HRMigration;

class DxUsersSalariesAddCustomProbationAction extends HRMigration
{
    private $table_name = "dx_users_salaries";
    
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function hr_up()
        {
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id;  
                    
            DB::transaction(function () use ($list_id){
                
                $act_id = DB::table('dx_actions')->insertGetId([
                            'title' => trans('db_' . $this->table_name . '.probation_salary_action'),
                            'code' => 'HR_SALARY_PROBATION'
                          ]);

                // Adjust fields
                $form = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
                
                DB::table('dx_forms_actions')->insert([
                    'form_id' => $form->id,
                    'action_id' => $act_id,
                    'is_after_save' => 1
                ]);                
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
                $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id;  
                $form = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();

                $act = DB::table('dx_actions')->where('code', '=', 'HR_SALARY_PROBATION')->first();

                if ($act) {
                    DB::table('dx_forms_actions')->where('action_id', '=', $act->id)->delete();
                    DB::table('dx_actions')->where('id', '=', $act->id)->delete();                    
                }
            });
        }
}

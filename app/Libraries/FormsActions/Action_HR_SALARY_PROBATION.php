<?php

namespace App\Libraries\FormsActions
{
    use DB;
    use \App\Exceptions;
    use Carbon\Carbon;
    use Config;
    use PDO;
    use Auth;

    /**
     * If probation salary entered then this action will generate 2 salaries rows
     * Action must be called after save
     */
    class Action_HR_SALARY_PROBATION extends Action
    {  
        
        /**
         * Sets db_table_name parameter
         */
        public function setTableName()
        {
            $this->db_table_name = ["dx_users_salaries"];
        }
        
        /**
         * Performs action
         */
        public function performAction()
        {            
            if ($this->request->input('item_id', 0)) {
                return; // we can perform action only on newly added record
            }

            $prob_sal = $this->request->input('probation_salary', null);
            $prob_sal_anual = $this->request->input('probation_salary_annual', null);
            $prob_month = $this->request->input('probation_months', 0);
            $valid_from = $this->request->input('valid_from', null);

            if (!$prob_sal && !$prob_month) {
                return; // no probation info
            }

            $this->validateProbation($prob_sal, $prob_month, $valid_from);
            $carb_valid_from = Carbon::createFromFormat('Y-m-d', check_date($valid_from, Config::get('dx.date_format')))->addMonths($prob_month);
            $carb_valid_to = $carb_valid_from->copy()->subDay();

            DB::setFetchMode(PDO::FETCH_ASSOC);
            
            $old_row = DB::table('dx_users_salaries')
                        ->where('id', '=', $this->item_id)
                        ->get();

            DB::setFetchMode(PDO::FETCH_CLASS);

            DB::table('dx_users_salaries')
            ->where('id', '=', $this->item_id)
            ->update([
                'valid_to' => $carb_valid_to->toDateString(),
                'salary' => $prob_sal,
                'annual_salary' => $prob_sal_anual,
                'probation_salary' => null,
                'probation_months' => 0,
                'probation_salary_annual' => null
            ]);

            $arr_data = [];
            foreach($old_row[0] as $key => $row) {
                if ($key == "id") {
                    continue;
                }

                if ($key == "probation_salary" || $key == "probation_salary_annual") {
                    $arr_data[$key] = null;
                    continue;
                }

                if ($key == "probation_months") {
                    $arr_data[$key] = 0;
                    continue;
                }
                
                if ($key == "valid_from") {
                    $arr_data[$key] = $carb_valid_from->toDateString();
                    continue;
                }

                $arr_data[$key] = $row;                
            }

            $new_id = DB::table('dx_users_salaries')->insertGetId($arr_data);

            DB::table('dx_db_events')->insertGetId([
                'type_id' => 1, // new item
                'user_id' => Auth::user()->id,
                'event_time' => date('Y-n-d H:i:s'),
                'list_id' => $this->list_id,
                'item_id' => $new_id
            ]);
            
        }

        /**
         * Validates probation period data
         * 
         * @param integer $prob_sal Probation period sallary
         * @param integer $prob_month Probation perion months
         * @param string $valid_from Salary record valid from date in string format
         * @return void
         */
        private function validateProbation($prob_sal, $prob_month, $valid_from) {
            if ($prob_sal && !$prob_month) {
                throw new Exceptions\DXCustomException(trans('db_dx_users_salaries.errors.probation_month_not_set'));
            }

            if ($prob_sal && !$prob_month) {
                throw new Exceptions\DXCustomException(trans('db_dx_users_salaries.errors.probation_salary_not_set'));
            }

            if (!$valid_from) {
                throw new Exceptions\DXCustomException(trans('db_dx_users_salaries.errors.valid_from_not_set'));
            }
        }

    }

}
<?php

namespace App\Libraries\Timeoff
{

    use DB;
    use App\Exceptions;  
    use Auth;
    use App\Libraries\DBHelper;
    use App\Libraries\Rights;

    
    /**
     * Class for employee time off calculation
     */
    class Timeoff
    {
        
        /**
         * Indicates if class is executed by system process (then we wont check rights)
         * @var boolean 
         */
        public $is_system_process = 0;
        
        /**
         * Class constructor
         * 
         * @param integer $employee_id Employee ID (from table dx_users)
         * @param integer $timeoff_type_id Time off type ID (from table dx_timeoff_types)
         */
        public function __construct($employee_id, $timeoff_type_id)
        {            
            $this->timeoff_type_id = $timeoff_type_id;
            $this->employee_id = $employee_id;
        }
        
        /**
         * Calculate time offs
         */
        public function calculate() {
            $this->checkRights();
            
            $policy_row = $this->getPolicyRow();           
             
            $algorithm = Algorithms\AlgorithmFactory::build_period($policy_row, $this->employee_id, $this->timeoff_type_id);
            $arr_time = $algorithm->getCalculated();
            
            if (count($arr_time) == 0) {
                return; // nothing calculated
            }
            
            $this->saveData($algorithm->first_date, $arr_time);
            
        }
        
        /**
         * Save calculated timeoff data in db
         * 
         * @param CarbonDate $first_date Date from which calculation started
         * @param Array $arr_time Array with calculated data to be saved
         */
        private function saveData($first_date, $arr_time) {
                
            $record_type_ACCRUED = $this->getRecordTypeID("ACCRUED");
            $record_type_LEAVE = $this->getRecordTypeID("LEAVE");
            
            DB::transaction(function () use ($first_date, $arr_time, $record_type_ACCRUED, $record_type_LEAVE) {
                // delete recalculated rows
                DB::table('dx_timeoff_calc')
                ->where('user_id', '=', $this->employee_id)
                ->where('timeoff_type_id', '=', $this->timeoff_type_id)
                ->where('calc_date', '>=', $first_date->toDateString())
                ->delete();
                
                foreach($arr_time as $item) {
                    DB::table('dx_timeoff_calc')->insert([
                        'user_id' => $this->employee_id,
                        'timeoff_type_id' => $this->timeoff_type_id,
                        'record_type_id' => $item['leave_id'] ? $record_type_LEAVE : $record_type_ACCRUED,
                        'calc_date' => $item['to'],
                        'from_date' => $item['from'],
                        'to_date' => $item['to'],
                        'balance' => $item['balance'],
                        'amount' => $item['amount'],
                        'leave_id' => $item['leave_id'] ? $item['leave_id'] : null,
                        'created_user_id' => $this->is_system_process ? null : Auth::user()->id,
                        'created_time' => date('Y-n-d H:i:s'),
                        'modified_user_id' => $this->is_system_process ? null : Auth::user()->id,
                        'modified_time' => date('Y-n-d H:i:s')
                    ]);
                }
            });
        }
        
        /**
         * Get's accrual policy row
         * 
         * @return object Accrual policy row
         * @throws Exceptions\DXCustomException
         */
        private function getPolicyRow() {
            $policy_row = DB::table('dx_users_accrual_policies as up')
                                ->select(
                                        'up.is_hiring_date', 
                                        'up.accrual_policy_id', 
                                        'up.id as user_policy_id', 
                                        'up.eff_date', 
                                        'co.code as co_code', 
                                        'm.nr as co_month_nr', 
                                        'd.code as co_day_code',
                                        'p.algorithm_code'
                                )
                                ->leftJoin('dx_accrual_policies as p', 'up.accrual_policy_id', '=', 'p.id')
                                ->leftJoin('dx_carryover_dates as co', 'p.carryover_date_id', '=', 'co.id')
                                ->leftJoin('dx_months as m', 'p.month_id', '=', 'm.id')
                                ->leftJoin('dx_month_days as d', 'p.month_day_id', '=', 'd.id')
                                ->where('up.timeoff_type_id', '=', $this->timeoff_type_id)
                                ->where('up.user_id', '=', $this->employee_id)
                                ->whereNull('up.end_date')
                                ->first();
            
            if (!$policy_row) {
                throw new Exceptions\DXCustomException(trans('errors.no_accrual_policy'));
            }
            
            return $policy_row;
        }
        
        /**
         * Checks user rights to perform calculation
         * 
         * @throws Exceptions\DXCustomException
         */
        private function checkRights() {
            
            if ($this->is_system_process) {
                return;
            }
            
            $list = DBHelper::getListByTable('dx_timeoff_calc');
            $right = Rights::getRightsOnList($list->id);
            
            if ($right == null) {
                throw new Exceptions\DXCustomException(trans('errors.no_rights_on_register'));
            }
           
            if ($right->is_new_rights == 0) {           
                throw new Exceptions\DXCustomException(trans('errors.no_rights_to_insert'));
            }
        }
        
         /**
         * Returns calculation record type ID by given code
         * 
         * @param string $type_code Record type code (from table dx_timeoff_records_types field code)
         * @return integer Record type ID
         * @throws Exceptions\DXCustomException
         */
        private function getRecordTypeID($type_code) {
            $type_row = DB::table('dx_timeoff_records_types')->where('code', '=', $type_code)->first();
            
            if (!$type_row) {
                throw new Exceptions\DXCustomException(sprintf(trans('errors.unsupported_factory_class'), $type_code ));
            }
            
            return $type_row->id;
        }        

    }
}
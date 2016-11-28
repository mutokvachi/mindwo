<?php

namespace App\Libraries\Timeoff
{

    use DB;
    use App\Exceptions;
    use Carbon\Carbon;
    use Config;    
    use Auth;
    use DateInterval;
    use Log;
    
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
         * Employee ID
         * @var integer
         */
        private $employee_id = 0;
        
        /**
         * Time off type ID
         * @var integer
         */
        private $timeoff_type_id = 0;
        
        /**
         * Accrual policy row
         * @var object
         */
        private $policy_row = null;
        
        /**
         * Employee row
         * @var object
         */
        private $employee_row = null;
        
        /**
         * Holidays rows
         * @var object
         */
        private $holidays_rows = null;
        
        /**
         * Accrual balance
         * @var decimal 
         */
        private $balance = 0;
        
        /**
         * Accrual levels rows
         * @var array
         */
        private $levels_rows = null;
        
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
            
            $this->setPolicyRow();
            $this->employee_row = DB::table('dx_users')->where('id', '=', $this->employee_id)->first();
            $this->setHolidaysArray();
            
            // get date from which to start calculation
            $calc_date = $this->getFirstCalcDate();
            $calc_date = $this->checkHolidays($calc_date);
            $calc_date_cur = $calc_date;
            
            $is_ok_to_loop = $this->isCalcDateOk($calc_date);
            $arr_time = [];
            while ($is_ok_to_loop) {                
                
                $calc_date_next = $this->checkHolidays($calc_date_cur->copy()->addDay());
                
                $diff = $calc_date_next->diffInDays($calc_date_cur);
                
                if ($diff > 1) {
                    array_push($arr_time, ['from' => $calc_date, 'to' => $calc_date_cur]);
                    $calc_date = $calc_date_next;
                    $calc_date_cur = $calc_date;
                }
                else {
                    $calc_date_cur = $calc_date_next;
                }
                
                $is_ok_to_loop = $this->isCalcDateOk($calc_date_cur);                
                
                if ($diff == 1 && !$is_ok_to_loop) {
                    array_push($arr_time, ['from' => $calc_date, 'to' => $calc_date_cur]);
                }
                
            }                        
            
            DB::transaction(function () use ($arr_time) {
                foreach($arr_time as $item) {
                    DB::table('dx_timeoff_calc')->insert([
                        'user_id' => $this->employee_id,
                        'timeoff_type_id' => $this->timeoff_type_id,
                        'record_type_id' => 1,
                        'calc_date' => $item['to'],
                        'from_date' => $item['from'],
                        'to_date' => $item['to']
                    ]);
                }
            });
        }
        
        /**
         * Loads holidays array
         */
        private function setHolidaysArray() {
            $rows = DB::table('dx_holidays as h')
                                   ->select('h.is_several_days', 'm1.nr as month_from_nr', 'd1.code as day_from_code', 'm2.nr as month_to_nr', 'd2.code as day_to_code')
                                   ->leftJoin('dx_months as m1', 'h.from_month_id', '=', 'm1.id')
                                   ->leftJoin('dx_month_days as d1', 'h.from_day_id', '=', 'd1.id')
                                   ->leftJoin('dx_months as m2', 'h.to_month_id', '=', 'm2.id')
                                   ->leftJoin('dx_month_days as d2', 'h.to_day_id', '=', 'd2.id')
                                   ->whereNull('h.country_id')
                                   ->orWhere('h.country_id', '=', $this->employee_row->doc_country_id)
                                   ->orderBy('m1.nr')
                                   ->orderBy('d1.code')
                                   ->get();            
            
            foreach($rows as $holiday) {                
                
                $holiday->date_from = $this->getDateFromCode($holiday->day_from_code, $holiday->month_from_nr);
                
                if (!$holiday->is_several_days) {
                    $holiday->date_to = $holiday->date_from;
                }
                else {
                    $holiday->date_to = $this->getDateFromCode($holiday->day_to_code, $holiday->month_to_nr);
                }
            }
            
            $this->holidays_rows =  json_decode(json_encode($rows), true); 
        }
        
        /**
         * Builds date from given month and day numbers
         * 
         * @param string $day_code Day number (or "LAST")
         * @param integer $month_nr Month number
         * @return DateTime
         */
        private function getDateFromCode($day_code, $month_nr) {
            $now = Carbon::now();
            if (is_numeric($day_code)) {
                    $dat = $now->year . '-' . $month_nr . '-' . $day_code;
            }
            else {
                // last day
                $dat_month = $now->year . '-' . $month_nr . '-01';
                $dat = date("Y-m-t", strtotime($dat_month));
            }
            
            return $dat;
        }
        
        /**
         * Sets accrual policy row
         * 
         * @throws Exceptions\DXCustomException
         */
        private function setPolicyRow() {
            $this->policy_row = DB::table('dx_users_accrual_policies as up')
                                ->select('up.accrual_policy_id', 'up.id as user_policy_id', 'up.eff_date', 'co.code as co_code', 'm.nr as co_month_nr', 'd.code as co_day_code')
                                ->leftJoin('dx_accrual_policies as p', 'up.accrual_policy_id', '=', 'p.id')
                                ->leftJoin('dx_carryover_dates as co', 'p.carryover_date_id', '=', 'co.id')
                                ->leftJoin('dx_months as m', 'p.month_id', '=', 'm.id')
                                ->leftJoin('dx_month_days as d', 'p.month_day_id', '=', 'd.id')
                                ->where('up.timeoff_type_id', '=', $this->timeoff_type_id)
                                ->whereNull('up.end_date')
                                ->first();
            
            if (!$this->policy_row) {
                throw new Exceptions\DXCustomException(trans('errors.no_accrual_policy'));
            }
            
            $this->setAccrualLevelsArr();
        }
        
        private function setAccrualLevelsArr() {
            $levels = DB::table('dx_accrual_levels as al')
                                 ->select(
                                         'al.start_moment', 
                                         'st.code as start_code', 
                                         'al.accrued_amount', 
                                         'at.code as type_code',
                                         'md.code as day_code',
                                         'al.max_accrual',
                                         'co.code as carry_over_code',
                                         'al.carryover_max'
                                 )
                                 ->leftJoin('dx_accrual_start_types as st', 'al.start_type_id', '=', 'st.id')
                                 ->leftJoin('dx_accrual_types as at', 'al.accrual_type_id', '=', 'at.id')
                                 ->leftJoin('dx_month_days as md', 'al.month_day_id', '=', 'md.id')
                                 ->leftJoin('dx_carryover_types as co', 'al.carryover_type_id', '=', 'co.id')
                                 ->where('al.accrual_policy_id', '=', $this->policy_row->accrual_policy_id)
                                 ->get();
            $arr_lev = [];
            foreach($levels as $level) {
                
            }
            
            $this->levels_rows = $arr_lev;
        }
        
        /**
         * Checks and gets if date is in holiday. If in holiday then gets next available date
         * We dont calculate holidays
         * 
         * @param DateTime $calc_date Date to be checked
         * @param integer $key Resursive iteration number
         * @return DateTime Available date to be calculated
         */
        private function checkHolidays($calc_date, $key = 0) {
            
            if ($calc_date->dayOfWeek == Carbon::SUNDAY || $calc_date->dayOfWeek == Carbon::SATURDAY) {
                return $this->checkHolidays($calc_date->copy()->addDay(), $key);
            }
            
            for ($i = $key; $i<count($this->holidays_rows); $i++) {
                if ($calc_date->between(Carbon::createFromFormat("Y-m-d",$this->holidays_rows[$i]['date_from']), Carbon::createFromFormat("Y-m-d",$this->holidays_rows[$i]['date_to']))) {
                    return $this->checkHolidays($calc_date->copy()->addDay(), $i++);
                }
            }
            
            return $calc_date;
        }
        
        /**
         * Checks if given date can be calculated
         * 
         * @param DateTime $calc_date Calculation date
         * @return boolean True - date can be calculated, False - date is after today or after employment termination date
         */
        private function isCalcDateOk($calc_date) {
            $now = Carbon::now();
            
            if ($calc_date->gte($now)) {
                return false;
            }
            
            if ($this->employee_row->termination_date) {
                $term_date = Carbon::createFromFormat('Y-m-d',$this->employee_row->termination_date);
                
                if ($calc_date->gte($term_date->date)) {
                    return false;
                }
            }
            
            return true;
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
         * Gets date from which to began calculation
         * If no calculation made jet, then will use accrual policy effective date
         * 
         * @return DateTime Date from which to start calculation
         */
        private function getFirstCalcDate() {
            $last_calc = DB::table('dx_timeoff_calc')
                        ->select('calc_date', 'balance')
                        ->where('user_id', '=', $this->employee_id)
                        ->orderBy('calc_date', 'DESC')
                        ->first();
            
            if ($last_calc) {
                $this->balance = $last_calc->balance;
            }
            
            return (!$last_calc) ? Carbon::createFromFormat('Y-m-d',$this->policy_row->eff_date) : Carbon::createFromFormat('Y-m-d', $last_calc->calc_date)->add(new DateInterval('P1D'));
        }
    }
}
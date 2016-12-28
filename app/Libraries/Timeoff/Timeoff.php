<?php

namespace App\Libraries\Timeoff
{

    use DB;
    use App\Exceptions;
    use Carbon\Carbon;    
    use Auth;
    use Log;
    use App\Libraries\DBHelper;
    use App\Libraries\Rights;
    use Config;
    
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
         * Current period accrued amount
         * @var decimal
         */
        private $cur_total = 0;
        
        /**
         * Accrual levels rows
         * @var array
         */
        private $levels_rows = null;
        
        /**
         * Accrued time array
         * @var array
         */
        private $arr_time = [];
        
        /**
         * Array with leaves information
         * @var array
         */
        private $leaves_rows = [];

        /**
         * ID for record type LEAVE
         * @var integer 
         */
        private $record_type_LEAVE = 0;
        
        /**
         * ID for record type ACCRUED
         * @var integer 
         */
        private $record_type_ACCRUED = 0;
        
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
            
            $this->employee_row = DB::table('dx_users')->where('id', '=', $this->employee_id)->first();
            $this->setPolicyRow();
            $this->setAccrualLevelsArr();
            $this->setHolidaysArray();
            $this->setRecordTypes();
            
            // get date from which to start calculation
            $first_date = $this->getFirstCalcDate();
            
            $now = Carbon::now(Config::get('dx.time_zone'));
            
            if ($first_date->gt($now)) {
                return; // allready all is calculated
            }
            
            $calc_date = $this->checkHolidays($first_date);
            $calc_date_cur = $calc_date;
            
            $this->setLeavesArray($calc_date); // we get only leaves which are equal or after calculation start date
            
            $is_ok_to_loop = $this->isCalcDateOk($calc_date);
                        
            $this->cur_total = 0;
            $cur_leave_stat = $this->isLeaveDay($calc_date);
            
            $this->setBalance($calc_date, true, $cur_leave_stat);
            
            // Calculate timeoff data
            while ($is_ok_to_loop) {
                $calc_date_next = $this->checkHolidays($calc_date_cur->copy()->addDay()); // we get next day for calculation (accroding to holidays which we skip)
                $next_leave_stat = $this->isLeaveDay($calc_date_next); // we calculate leave ID (can be 0 if no leave) for next day
                
                $diff = $calc_date_next->diffInDays($calc_date_cur); // we check if there was skipped days because of holidays
                
                if ($diff > 1 || $cur_leave_stat != $next_leave_stat) {
                    // there was skipped days because of holidays or there is changed leave status (started leave period or ended leave period)
                    $this->putAccrual($calc_date, $calc_date_cur, $cur_leave_stat);
                    
                    // here we change calculation period starting date
                    $calc_date = $calc_date_next;
                    $calc_date_cur = $calc_date;
                    $cur_leave_stat = $next_leave_stat;
                    
                    // calculate balance (we provide 2nd argment "false" because we wont acumulate amount for this period (we started new period from 0)
                    $this->setBalance($calc_date, false, $cur_leave_stat);
                }
                else {
                    $calc_date_cur = $calc_date_next;
                    
                    // calculate balance (we provide 2nd argument "true" because we will accumulate amount for current period which still continues)
                    $this->setBalance($calc_date_cur, true, $cur_leave_stat);
                }
                
                $is_ok_to_loop = $this->isCalcDateOk($calc_date_cur); // here we ensure that day is not after today and there employee is not terminated - in both cases we stop calculation               
                
                if ($diff == 1 && !$is_ok_to_loop) {
                    // we stop calculation and put in data array 1 day which was allready calculated but not in array
                    $this->putAccrual($calc_date,$calc_date_cur, $cur_leave_stat);
                    // after this we will quit the loop
                }                
            }                        
            
            // Save calculated timeoff data in db
            DB::transaction(function () use ($first_date) {
                // delete recalculated rows
                DB::table('dx_timeoff_calc')
                ->where('user_id', '=', $this->employee_id)
                ->where('timeoff_type_id', '=', $this->timeoff_type_id)
                ->where('calc_date', '>=', $first_date->toDateString())
                ->delete();
                
                foreach($this->arr_time as $item) {
                    DB::table('dx_timeoff_calc')->insert([
                        'user_id' => $this->employee_id,
                        'timeoff_type_id' => $this->timeoff_type_id,
                        'record_type_id' => $item['leave_id'] ? $this->record_type_LEAVE : $this->record_type_ACCRUED,
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
         * Set current accrued balance (global variables)
         * 
         * @param DateTime $calc_date Date for calculation
         * @param boolean $is_cur_acumulate True - accrued amount will be accumulated for current period
         * @param integer $leave_id Leave ID (or 0 if no leave)
         */
        private function setBalance($calc_date, $is_cur_acumulate = true, $leave_id) {
            
            $accrued_amount = 0;
            if ($leave_id) {
                $accrued_amount = - Config::get('dx.working_day_h', 8); // working day is 8 hours
            }
            else {                
                $level = $this->getLevel($calc_date);
                if ($level['period']->isAccruable($calc_date)) {
                    $accrued_amount = $level['accrued_amount'];
                }
            }
            
            $this->balance += $accrued_amount;
                
            if (!$is_cur_acumulate) {
                $this->cur_total = 0;
            }
            $this->cur_total += $accrued_amount;              
        }
        
        /**
         * Put accrued amount for given period into array for later saving into db
         * 
         * @param DateTime $from_date From date
         * @param DateTime $to_date To date,
         * @param integer $leave_id Leave ID (or 0 if no leave)
         */
        private function putAccrual($from_date, $to_date, $leave_id) {
            array_push($this->arr_time, [
                'from' => $from_date,
                'to' => $to_date,
                'balance' => $this->balance,
                'amount' => $this->cur_total,
                'leave_id' => $leave_id
            ]);
        }
        
        /**
         * Get accrual policy level by given date
         * 
         * @param DateTime $dat Date for calculation
         * @return object Accrual level object
         */
        private function getLevel($dat) {
            foreach($this->levels_rows as $level) {                      
                if ($dat->between($level['from_date'], $level['to_date'])) {
                    return $level;
                }
            }
            return null;
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
        
        /**
         * Sets all record types IDs
         */
        private function setRecordTypes() {
            $this->record_type_ACCRUED = $this->getRecordTypeID("ACCRUED");
            $this->record_type_LEAVE = $this->getRecordTypeID("LEAVE");
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
                
                $holiday->date_from = \App\Libraries\Helper::getDateFromCode($holiday->day_from_code, $holiday->month_from_nr);
                
                if (!$holiday->is_several_days) {
                    $holiday->date_to = $holiday->date_from;
                }
                else {
                    $holiday->date_to = \App\Libraries\Helper::getDateFromCode($holiday->day_to_code, $holiday->month_to_nr);
                }
            }
            
            $this->holidays_rows =  json_decode(json_encode($rows), true);            
            
        }
        
        /**
         * Loades leaves array
         * @param DateTime $calc_date Date from which to load leaves
         */
        private function setLeavesArray($calc_date) {
            $this->leaves_rows = DB::table('dx_users_left as ul')
                                    ->select('ul.id', 'ul.left_from', 'ul.left_to')
                                    ->where('ul.left_reason_id', '=', $this->timeoff_type_id)
                                    ->where('ul.user_id', '=', $this->employee_id)
                                    ->where('ul.left_from', '>=', $calc_date->toDateString())
                                    ->orderBy('ul.left_from')
                                    ->get();
        }
        
        /**
         * Sets accrual policy row
         * 
         * @throws Exceptions\DXCustomException
         */
        private function setPolicyRow() {
            $this->policy_row = DB::table('dx_users_accrual_policies as up')
                                ->select('up.is_hiring_date', 'up.accrual_policy_id', 'up.id as user_policy_id', 'up.eff_date', 'co.code as co_code', 'm.nr as co_month_nr', 'd.code as co_day_code')
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
        }
        
        /**
         * Fill accrual levels array
         */
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
                                 ->orderBy('al.id')
                                 ->get();
            
            $now = Carbon::now(Config::get('dx.time_zone'))->copy()->addDay();
            $arr_lev = [];
            foreach($levels as $key => $level) {
                $start = AccrualStart\AccrualStartFactory::build_start($level, $this->policy_row, $this->employee_row);                              
                
                array_push($arr_lev, [
                    'from_date' => $start->getFromDate(),
                    'to_date' => $now,
                    'accrued_amount' => $level->accrued_amount,
                    'period' => AccrualPeriod\AccrualPeriodFactory::build_period($level, $this->employee_row),
                    'max_accrual' => $level->max_accrual,
                    'carry_over_code' => $level->carry_over_code,
                    'carryover_max' => $level->carryover_max
                ]);
                
                if ($key > 0) {
                    $arr_lev[$key-1]['to_date'] = $arr_lev[$key]['from_date']->copy()-subDay(); 
                }
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
                    return $this->checkHolidays($calc_date->copy()->addDay(), $i);
                }
            }
            
            return $calc_date;
        }
        
        /**
         * Check if calculation date falls in leave interval
         * 
         * @param DateTime $calc_date Calculation date
         * @return integer Greater than 0 - is leave day (leave id), 0 - not leave
         */
        private function isLeaveDay($calc_date) {
            foreach($this->leaves_rows as $leave) {                
                if ($calc_date->between(Carbon::createFromFormat("Y-m-d", $leave->left_from), Carbon::createFromFormat("Y-m-d", $leave->left_to))) {                    
                    return $leave->id;
                }
            }
            
            return 0;
        }
        
        /**
         * Checks if given date can be calculated
         * 
         * @param DateTime $calc_date Calculation date
         * @return boolean True - date can be calculated, False - date is after today or after employment termination date
         */
        private function isCalcDateOk($calc_date) {
            $now = Carbon::now(Config::get('dx.time_zone'));
            
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
            
            $missing_leave_date = $this->getUncalculatedLeaveDate();
            
            $last_calc = DB::table('dx_timeoff_calc')
                        ->select('calc_date', 'balance')
                        ->where('user_id', '=', $this->employee_id)
                        ->where('timeoff_type_id', '=', $this->timeoff_type_id)
                        ->where(function($query) use ($missing_leave_date) {
                            if ($missing_leave_date) {
                                $query->where('calc_date', '<', $missing_leave_date);
                            }
                        })
                        ->orderBy('calc_date', 'DESC')
                        ->first();            
            
            if ($last_calc) {
                $this->balance = $last_calc->balance;
            }
            
            $eff_date = ($this->policy_row->is_hiring_date) ? $this->employee_row->join_date : $this->policy_row->eff_date;
            
            if (!$eff_date) {
                throw new Exceptions\DXCustomException(trans('errors.no_joined_date'));
            }
            
            return (!$last_calc) ? Carbon::createFromFormat('Y-m-d', $eff_date) : Carbon::createFromFormat('Y-m-d', $last_calc->calc_date)->addDay();
        }
        
        /**
         * Check if there is an leave not included in calculation of an calculation row related to missing leve (which might be delated)
         * In case we have such info - then we need to recalculate timeoffs which are after the date of missing row
         * 
         * @return mixed The date from which we neeed to recalculate timeoffs (or null if no recalculation needed)
         */
        private function getUncalculatedLeaveDate() {
            // check if there are some leave which is not included in calculation
            $missing_calc = DB::table('dx_users_left as ul')
                                ->select('ul.left_from')
                                ->leftJoin('dx_timeoff_calc as to', 'ul.id', '=', 'to.leave_id')
                                ->where('ul.user_id', '=', $this->employee_id)
                                ->where('ul.left_reason_id', '=', $this->timeoff_type_id)
                                ->whereNull('to.id')
                                ->orderBy('ul.left_from')
                                ->first();
            
            // check if there are some deleted leave which were included in calculation
            $missing_leave = DB::table('dx_timeoff_calc as to')
                                ->select('to.calc_date')
                                ->leftJoin('dx_users_left as ul', 'ul.id', '=', 'to.leave_id')
                                ->where('to.user_id', '=', $this->employee_id)
                                ->where('to.timeoff_type_id', '=', $this->timeoff_type_id)
                                ->whereNotNull('to.leave_id')
                                ->whereNull('ul.id')
                                ->orderBy('to.calc_date')
                                ->first();
            
            if ($missing_calc || $missing_leave) {
                
                $calc_date = Carbon::createFromFormat('Y-m-d', $missing_calc ? $missing_calc->left_from : date('Y-m-d'));
                $leave_date = Carbon::createFromFormat('Y-m-d', $missing_leave ? $missing_leave->calc_date : date('Y-m-d'));
                
                // compare dates - we return smaller
                if ($calc_date->gt($leave_date)) {
                    return $leave_date->toDateString();
                }
                else {
                    return $calc_date->toDateString();
                }                
            }
            
            return null;
        }
    }
}
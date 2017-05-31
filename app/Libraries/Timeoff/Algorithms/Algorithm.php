<?php

namespace App\Libraries\Timeoff\Algorithms
{
    use DB;
    use Carbon\Carbon;
    use App\Exceptions;  
    use Config;
    
    /**
     * Accrual algorithm object class
     */
    abstract class Algorithm
    {  
        /**
         * Employee ID for which to calculate
         * @var integer 
         */
        public $employee_id = 0;
        
        /**
         * Employee data row (from table dx_users)
         * @var object 
         */
        public $employee_row = null;        
        
        /**
         * Date from which to start calculation
         * @var CarbonDate 
         */
        public $first_date = null;
        
         /**
         * Time off type ID (from table dx_timeoff_types)
         * @var integer
         */
        public $timeoff_type_id = 0;
        
        /**
         * Accrual policy row
         * @var object
         */
        public $policy_row = null;
        
        /**
         * Accrual balance
         * @var decimal 
         */
        public $balance = 0;
        
        /**
         * Accrual levels rows
         * @var array
         */
        public $levels_rows = null;
        
        /**
         * Array with leaves information
         * @var array
         */
        private $leaves_rows = [];
        
               
        /**
         * Current period accrued amount
         * @var decimal
         */
        public $cur_total = 0;        
                       
        /**
         * Accrued time array
         * @var array
         */
        public $arr_time = [];
               
        /**
         * Indicates if accrual period included holidays
         * @var boolean
         */
        public $is_holidays_in = false;
        
        /**
         * Perform calculation - fills array $this->arr_time with values
         */
        abstract protected function doCalculation();
                
        /**
         * Class constructor
         * 
         * @param integer  $employee_id Employee ID
         * @param integer  $timeoff_type_id Timeoff type ID (vacation, sick etc)
         * @param object   $policy_row Accrual policy row
         */
        public function __construct($employee_id, $timeoff_type_id, $policy_row)
        {
            $this->timeoff_type_id = $timeoff_type_id;
            $this->employee_id = $employee_id;
            $this->policy_row = $policy_row;
            
            $this->employee_row = DB::table('dx_users')->where('id', '=', $this->employee_id)->first();
            
            // get date from which to start calculation
            $this->first_date = $this->getFirstCalcDate();
        }
        
         /**
         * Returns array with calculated accrued data
         * @return Array
         */
        public function getCalculated() {
            $now = Carbon::now(Config::get('dx.time_zone'));
            
            if ($this->first_date->gt($now)) {
                return []; // allready all is calculated
            }
            
            $this->setAccrualLevelsArr();
            
            $this->doCalculation();
            
            return $this->arr_time;
        }
        
        /**
         * Loades leaves array
         * @param DateTime $calc_date Date from which to load leaves
         */
        public function setLeavesArray($calc_date) {
            $this->leaves_rows = DB::table('dx_users_left as ul')
                                    ->select('ul.id', 'ul.left_from', 'ul.left_to')
                                    ->where('ul.left_reason_id', '=', $this->timeoff_type_id)
                                    ->where('ul.user_id', '=', $this->employee_id)
                                    ->where('ul.left_from', '>=', $calc_date->toDateString())
                                    ->orderBy('ul.left_from')
                                    ->get();
        }
        
        /**
         * Put accrued amount for given period into array for later saving into db
         * 
         * @param DateTime $from_date From date
         * @param DateTime $to_date To date,
         * @param integer $leave_id Leave ID (or 0 if no leave)
         */
        public function putAccrual($from_date, $to_date, $leave_id) {
            
            if ($this->cur_total == 0) {
                return;
            }
            
            array_push($this->arr_time, [
                'from' => $from_date,
                'to' => $to_date,
                'balance' => $this->balance,
                'amount' => $this->cur_total,
                'leave_id' => $leave_id
            ]);
        }
        
        /**
         * Set current accrued balance (global variables)
         * 
         * @param DateTime $calc_date Date for calculation
         * @param boolean $is_cur_acumulate True - accrued amount will be accumulated for current period
         * @param integer $leave_id Leave ID (or 0 if no leave)
         */
        public function setBalance($calc_date, $is_cur_acumulate = true, $leave_id) {
            
            $accrued_amount = 0;
            if ($leave_id) {
                $accrued_amount = - Config::get('dx.working_day_h', 8); // working day is 8 hours
            }
            else {                
                $level = $this->getLevel($calc_date);
                if ($level) {
                    if ($level['period']->isAccruable($calc_date)) {
                        $accrued_amount = $level['accrued_amount'];
                    }
                    
                    $this->is_holidays_in = $level['period']->is_holidays_in;
                }
            }
            
            $this->balance += $accrued_amount;
                
            if (!$is_cur_acumulate) {
                $this->cur_total = 0;
            }
            $this->cur_total += $accrued_amount;              
        }
        
        /**
         * Checks if given date can be calculated
         * 
         * @param DateTime $calc_date Calculation date
         * @return boolean True - date can be calculated, False - date is after today or after employment termination date
         */
        public function isCalcDateOk($calc_date) {
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
         * Check if calculation date falls in leave interval
         * 
         * @param DateTime $calc_date Calculation date
         * @return integer Greater than 0 - is leave day (leave id), 0 - not leave
         */
        public function isLeaveDay($calc_date) {
            
            foreach($this->leaves_rows as $leave) {                
                if ($calc_date->between(Carbon::createFromFormat("Y-m-d", $leave->left_from), Carbon::createFromFormat("Y-m-d", $leave->left_to))) {                    
                    
                    return $leave->id;
                }
            }
            
            return 0;
        }
        
        /**
         * Get accrual policy level by given date
         * 
         * @param DateTime $dat Date for calculation
         * @return object Accrual level object
         */
        public function getLevel($dat) {
            foreach($this->levels_rows as $level) {                      
                if ($dat->between($level['from_date'], $level['to_date'])) {
                    return $level;
                }
            }
            return null;
        }
        
        /**
         * Fill accrual levels array
         */
        public function setAccrualLevelsArr() {
            
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
                $start = \App\Libraries\Timeoff\AccrualStart\AccrualStartFactory::build_start($level, $this->policy_row, $this->employee_row);                              
                
                array_push($arr_lev, [
                    'from_date' => $start->getFromDate(),
                    'to_date' => $now,
                    'accrued_amount' => $level->accrued_amount,
                    'period' => \App\Libraries\Timeoff\AccrualPeriod\AccrualPeriodFactory::build_period($level, $this->employee_row),
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
         * Gets date from which to began calculation
         * If no calculation made jet, then will use accrual policy effective date
         * 
         * @return DateTime Date from which to start calculation
         */
        public function getFirstCalcDate() {
            
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
            // check if there are some new leave which is not included in calculation
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
            
            // check if there are some update leaves which were included in calculation
            $updated_leave = DB::table('dx_timeoff_calc as to')
                                ->select('to.calc_date')
                                ->join('dx_users_left as ul', 'ul.id', '=', 'to.leave_id')
                                ->where('to.user_id', '=', $this->employee_id)
                                ->where('to.timeoff_type_id', '=', $this->timeoff_type_id)
                                ->whereNotNull('to.leave_id')
                                ->whereRaw('ul.modified_time > to.modified_time')
                                ->orderBy('to.calc_date')
                                ->first();
            
            if ($missing_calc || $missing_leave || $updated_leave) {
                
                $calc_date = Carbon::createFromFormat('Y-m-d', $missing_calc ? $missing_calc->left_from : date('Y-m-d'));
                $leave_date = Carbon::createFromFormat('Y-m-d', $missing_leave ? $missing_leave->calc_date : date('Y-m-d'));
                $update_date = Carbon::createFromFormat('Y-m-d', $updated_leave ? $updated_leave->calc_date : date('Y-m-d'));                
                
                // compare dates - we return smaller
                if ($calc_date->gt($leave_date)) {
                    $dat1 = $leave_date;
                }
                else {
                    $dat1 = $calc_date;
                }  
                
                if ($dat1->gt($update_date)) {
                    return $update_date->toDateString();
                }
                else {
                    return $dat1->toDateString();
                }  
            }
            
            return null;
        }

    }

}

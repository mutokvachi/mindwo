<?php

namespace App\Libraries\Timeoff\Algorithms
{

    use Carbon\Carbon;
    use Config;
    use DB;
    use App\Exceptions;  
    
    /**
     * Accrual algorithm for daily calculations
     */
    class Algorithm_DAILY extends Algorithm
    {       
        
        /**
         * Holidays rows
         * @var object
         */
        private $holidays_rows = null;
                
        /**
         * Fills array $this->arr_time with calculated accrued data
         */
        public function doCalculation()
        {            
            $this->holidays_rows = \App\Libraries\Helper::getHolidaysArray($this->employee_row->doc_country_id);
               
            $calc_date = $this->checkHolidays($this->first_date);
            $calc_date_cur = $calc_date;
                        
            $this->setLeavesArray($calc_date); // we get only leaves which are equal or after calculation start date
            
            $is_ok_to_loop = $this->isCalcDateOk($calc_date);                        
            
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
            
            if ($this->is_holidays_in) {
                return $calc_date;
            }
            
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

    }

}
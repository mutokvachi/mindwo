<?php

namespace App\Libraries\Timeoff\Algorithms
{

    use Carbon\Carbon;
    use Config;
    
    /**
     * Accrual algorithm for Georgia
     * 
     * 24 days/year
     * It count - 2 days per month
     * If employee start after 15  - he will have 1 day leave this month
     * If employee starts to work at the last day of month (for example 29 or 30) then he allready earned 1 day for leave
     */
    class Algorithm_GEORGIA extends Algorithm
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
            
            $calc_date = $this->first_date;
            $calc_date_cur = $calc_date;
                        
            $this->setLeavesArray($calc_date); // we get only leaves which are equal or after calculation start date

            $this->handleJoining();
                        
            $is_ok_to_loop = $this->isCalcDateOk($calc_date);
            $cur_leave_stat = $this->isLeaveDay($calc_date);
            
            // Calculate timeoff data
            while ($is_ok_to_loop) {                
                
                if ($cur_leave_stat == 0) {
                    $calc_date_next = $calc_date_cur->copy()->addDay(); // we get next day for calculation
                    $diff = 1;
                }
                else {
                    $calc_date_next = $this->checkHolidays($calc_date_cur->copy()->addDay());
                    $diff = $calc_date_next->diffInDays($calc_date_cur); // we check if there was skipped days because of holidays
                }
                
                $next_leave_stat = $this->isLeaveDay($calc_date_next); // we calculate leave ID (can be 0 if no leave) for next day
                
                $is_next_month = ($calc_date_next->month != $calc_date_cur->month); //ToDo: use day from accrual settings
               
                if ($diff > 1 || $is_next_month || $cur_leave_stat != $next_leave_stat) {
                    // there was new month started or there is changed leave status (started leave period or ended leave period)
                    $this->putAccrual($calc_date, $calc_date_cur, $cur_leave_stat);
                    
                    // here we change calculation period starting date
                    $old_calc = $calc_date_cur;
                    $old_stat = $cur_leave_stat;
                    $calc_date = $calc_date_next;
                    $calc_date_cur = $calc_date;
                    $cur_leave_stat = $next_leave_stat;
                   
                    // calculate balance (we provide 2nd argment "false" because we wont acumulate amount for this period (we started new period from 0)
                    $this->setBalance($calc_date, false, $cur_leave_stat);
                    
                    if ($is_next_month && $old_stat > 0) {
                        $old_tot = $this->cur_total;
                        $this->setBalance($old_calc, false, 0);
                        $this->putAccrual($old_calc, $old_calc, 0);
                        $this->cur_total = $old_tot;
                    }
                }
                else {
                    $calc_date_cur = $calc_date_next;
                    
                    // calculate balance (we provide 2nd argument "true" because we will accumulate amount for current period which still continues)
                    $this->setBalance($calc_date_cur, true, $cur_leave_stat);                   
                }                
                                
                $is_ok_to_loop = $this->isCalcDateOk($calc_date_cur); // here we ensure that day is not after today and there employee is not terminated - in both cases we stop calculation               
                
                if (!$is_next_month && !$is_ok_to_loop) {
                    // we stop calculation and put in data array 1 day which was allready calculated but not in array
                    $this->putAccrual($calc_date, $calc_date_cur, $cur_leave_stat);                    
                    // after this we will quit the loop
                }                
            }        
            
            return $this->arr_time;
        }
        
        private function handleJoining() {
            $join_date = Carbon::createFromFormat('Y-m-d',$this->employee_row->join_date);
            
            if ($join_date->eq($this->first_date) && $join_date->day > 15) {
                $level = $this->getLevel($this->first_date);
                if ($level) {
                    $accrued_amount = $level['accrued_amount'];
                    $this->balance = -$accrued_amount/2;
                    $this->cur_total = -$accrued_amount/2;
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
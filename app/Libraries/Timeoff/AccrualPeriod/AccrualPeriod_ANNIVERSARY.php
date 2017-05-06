<?php

namespace App\Libraries\Timeoff\AccrualPeriod
{

    use Carbon\Carbon;

    /**
     * Accrual start period - Anniversary
     */
    class AccrualPeriod_ANNIVERSARY extends AccrualPeriod
    {

        /**
         * Returns if accrual level can be accrued
         * 
         * @return boolean
         */
        public function isAccruable($calc_date)
        {
            $dat = Carbon::createFromFormat("Y-m-d", $this->employee_row->join_date)->addYear();

            return ($calc_date->gte($dat) && $calc_date->day == $dat->day && $calc_date->month == $dat->month);
        }
        
        /**
         * Set's if holidays are included in accrual
         */
        public function setHolidaysIn() {
            $this->is_holidays_in = true;
        }

    }

}
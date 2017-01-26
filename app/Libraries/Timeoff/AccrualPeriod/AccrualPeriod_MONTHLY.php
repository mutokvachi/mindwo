<?php

namespace App\Libraries\Timeoff\AccrualPeriod
{

    use Carbon\Carbon;
    use Config;
    
    /**
     * Accrual start period - Monthly
     */
    class AccrualPeriod_MONTHLY extends AccrualPeriod
    {

        /**
         * Returns if accrual level can be accrued
         * 
         * @return boolean
         */
        public function isAccruable($calc_date)
        {
            $now = Carbon::now(Config::get('dx.time_zone'));
            $dat = Carbon::createFromFormat("Y-m-d", \App\Libraries\Helper::getDateFromCode(null, $this->level_row->day_code, $now->month));

            return ($calc_date->day == $dat->day && $calc_date->month == $dat->month);
        }

    }

}
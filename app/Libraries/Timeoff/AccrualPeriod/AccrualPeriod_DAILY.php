<?php

namespace App\Libraries\Timeoff\AccrualPeriod
{

    /**
     * Accrual start period - Daily
     */
    class AccrualPeriod_DAILY extends AccrualPeriod
    {

        /**
         * Returns if accrual level can be accrued
         * 
         * @return boolean
         */
        public function isAccruable($calc_date)
        {
            return true; // we can accrue each day
        }

        /**
         * Set's if holidays are included in accrual
         */
        public function setHolidaysIn() {
            $this->is_holidays_in = false;
        }
    }

}
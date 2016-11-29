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
        public function isAccruable()
        {
            return true; // we can accrue each day
        }

    }

}
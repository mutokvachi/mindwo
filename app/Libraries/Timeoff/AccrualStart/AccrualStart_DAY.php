<?php

namespace App\Libraries\Timeoff\AccrualStart
{
    use Carbon\Carbon;   
    
    /**
     * Accrual start period - Day
     */
    class AccrualStart_DAY extends AccrualStart
    {
        /**
         * Returns accrual level start date
         * 
         * @return DateTime
         */
        public function getFromDate() {
            
            $dat = Carbon::createFromFormat('Y-m-d', $this->eff_date)->subDay();
            
            return $dat->copy()->addDays($this->level_row->start_moment);
        }

    }

}
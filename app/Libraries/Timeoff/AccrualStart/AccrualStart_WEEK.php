<?php

namespace App\Libraries\Timeoff\AccrualStart
{
    use Carbon\Carbon;
    
    /**
     * Accrual start period - Week
     */
    class AccrualStart_WEEK extends AccrualStart
    {
        /**
         * Returns accrual level start date
         * 
         * @return DateTime
         */
        public function getFromDate() {
            $dat = Carbon::createFromFormat('Y-m-d',$this->policy_row->eff_date);
            
            return $dat->copy()->addWeeks($this->level_row->start_moment);
        }

    }

}
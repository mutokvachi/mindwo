<?php

namespace App\Libraries\Timeoff\AccrualStart
{
    use Carbon\Carbon;
    
    /**
     * Accrual start period - Year
     */
    class AccrualStart_YEAR extends AccrualStart
    {
        /**
         * Returns accrual level start date
         * 
         * @return DateTime
         */
        public function getFromDate() {
            $dat = Carbon::createFromFormat('Y-m-d',$this->policy_row->eff_date);
            
            return $dat->copy()->addYears($this->level_row->start_moment);
        }

    }

}
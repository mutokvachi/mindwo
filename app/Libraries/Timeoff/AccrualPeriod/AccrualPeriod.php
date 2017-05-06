<?php

namespace App\Libraries\Timeoff\AccrualPeriod
{

    /**
     * Accrual period object class
     */
    abstract class AccrualPeriod
    {        
        /**
         * Accrual policy level data row (from table dx_accrual_levels, joined related tables as well)
         * @var object 
         */
        public $level_row = null;
        
        /**
         * Employee data row (from table dx_users)
         * @var object 
         */
        public $employee_row = null;
        
        /**
         * Indicates if accrual period included holidays
         * @var boolean
         */
        public $is_holidays_in = false;
        
        /**
         * Returns if accrual level can be accrued
         */
        abstract function isAccruable($calc_date);
        
        /**
         * Set's if holidays are included in accrual
         */
        abstract protected function setHolidaysIn();
                
        /**
         * Class constructor
         * 
         * @param object  $level_row Level data row
         * @param object  $policy_row Accrual policy data row
         */
        public function __construct($level_row, $employee_row)
        {
            $this->level_row = $level_row;
            $this->employee_row = $employee_row;
            
            $this->setHolidaysIn();
        }

    }

}

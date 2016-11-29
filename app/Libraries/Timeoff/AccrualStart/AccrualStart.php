<?php

namespace App\Libraries\Timeoff\AccrualStart
{
    use App\Exceptions;
    use Log;
    /**
     * Accrual start object class
     */
    abstract class AccrualStart
    {        
        /**
         * Accrual policy level data row (from table dx_accrual_levels, joined related tables as well)
         * @var object 
         */
        public $level_row = null;
        
        /**
         * Accrual policy data row (from table dx_users_accrual_policies)
         * @var object 
         */
        public $policy_row = null;
        
        /**
         * Employee data row (from table dx_users)
         * @var type 
         */
        public $employee_row = null;
        
        /**
         * Date from which to starts calculations for employee (usually its joining date)
         * @var type 
         */
        public $eff_date = null;
        
        /**
         * Returns accrual level start date
         */
        abstract function getFromDate();
                
        /**
         * Class constructor
         * 
         * @param object  $level_row Level data row
         * @param object  $policy_row Accrual policy data row
         * @param object  $employee_row Employee data row
         */
        public function __construct($level_row, $policy_row, $employee_row)
        {
            $this->level_row = $level_row;
            $this->policy_row = $policy_row;
            $this->employee_row = $employee_row;
            
            $this->eff_date = ($this->policy_row->is_hiring_date) ? $this->employee_row->join_date : $this->policy_row->eff_date;
            
            if (!$this->eff_date) {
                throw new Exceptions\DXCustomException(trans('errors.no_joined_date'));
            }
        }

    }

}

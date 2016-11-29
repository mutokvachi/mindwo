<?php

namespace App\Libraries\Timeoff\AccrualStart
{
    use App\Exceptions;
    
    /**
     * Accrual period class factory
     */
    class AccrualStartFactory
    {
        /**
         * Class builder
         * 
         * @param object  $level_row Level data row
         * @param object  $policy_row Accrual policy data row
         * @param object  $employee_row Employee data row
         */
        public static function build_start($level_row, $policy_row, $employee_row)
        {
            $class = "App\\Libraries\\Timeoff\\AccrualStart\\AccrualStart_" . $level_row->start_code;

            if (!class_exists($class)) {
                throw new Exceptions\DXCustomException(sprintf(trans('errors.unsupported_factory_class'), $level_row->start_code ));
            }

            return new $class($level_row, $policy_row, $employee_row);
        }

    }

}
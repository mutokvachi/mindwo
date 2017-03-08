<?php

namespace App\Libraries\Timeoff\AccrualPeriod
{
    use App\Exceptions;
    
    /**
     * Accrual period class factory
     */
    class AccrualPeriodFactory
    {
        /**
         * Class builder
         * 
         * @param object  $level_row Level data row
         * @param object  $employee_row Accrual employee data row
         */
        public static function build_period($level_row, $employee_row)
        {
            $class = "App\\Libraries\\Timeoff\\AccrualPeriod\\AccrualPeriod_" . $level_row->type_code;

            if (!class_exists($class)) {
                throw new Exceptions\DXCustomException(sprintf(trans('errors.unsupported_factory_class'), $level_row->type_code ));
            }

            return new $class($level_row, $employee_row);
        }

    }

}
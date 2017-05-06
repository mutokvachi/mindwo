<?php

namespace App\Libraries\Timeoff\Algorithms
{
    use App\Exceptions;
    
    /**
     * Accrual period class factory
     */
    class AlgorithmFactory
    {
        /**
         * Class builder
         * 
         * @param object   $policy_row Accrual policy row
         * @param integer  $employee_id Employee ID
         * @param integer  $timeoff_type_id Timeoff type ID (vacation, sick etc)
         */
        public static function build_period($policy_row, $employee_id, $timeoff_type_id)
        {
            $algorithm_code = ($policy_row->algorithm_code) ? $policy_row->algorithm_code : "DAILY";
            
            $class = "App\\Libraries\\Timeoff\\Algorithms\\Algorithm_" . $algorithm_code;

            if (!class_exists($class)) {
                throw new Exceptions\DXCustomException(sprintf(trans('errors.unsupported_factory_class'), $algorithm_code));
            }

            return new $class($employee_id, $timeoff_type_id, $policy_row);
        }

    }

}
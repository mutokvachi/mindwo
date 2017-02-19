<?php
namespace App\Exceptions 
{    
    /**
     * Error raised when employee name cant be splited by first name and last name
     */
    class DXImportEmployeeNameException extends \Exception 
    {
        public function __construct($empl_name) {
            $message = sprintf(trans('errors.employee_name_exception'), $empl_name);
            $code = 1;
            $previous = null;

            // make sure everything is assigned properly
            parent::__construct($message, $code, $previous);
        }
    }
}
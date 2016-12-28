<?php
namespace App\Exceptions 
{    
    /**
     * Error raised when on update there are no fields changed
     */
    class DXNoRepresentField extends \Exception 
    {
        public function __construct() {
            $message = trans('errors.no_represent_field');
            $code = 1;
            $previous = null;

            // make sure everything is assigned properly
            parent::__construct($message, $code, $previous);
        }
    }
}
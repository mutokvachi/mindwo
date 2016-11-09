<?php
namespace App\Exceptions 
{    
    /**
     * Error raised when on update there are no fields changed
     */
    class DXHistoryNoChanges extends \Exception 
    {
        public function __construct() {
            $message = trans('errors.nothing_changed');
            $code = 1;
            $previous = null;

            // make sure everything is assigned properly
            parent::__construct($message, $code, $previous);
        }
    }
}
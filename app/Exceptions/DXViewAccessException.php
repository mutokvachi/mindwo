<?php
namespace App\Exceptions 
{
    use Symfony\Component\HttpKernel\Exception;
    use Log;
    
    class DXViewAccessException extends \Exception 
    {
        public function __construct() {
            $message = "View access exception - public user try to access secure view!";
            $code = 1;
            $previous = null;

            // make sure everything is assigned properly
            parent::__construct($message, $code, $previous);
        }
    }
}
<?php
namespace App\Exceptions 
{
    use Symfony\Component\HttpKernel\Exception;
    
    /**
     * Raised wen importing from Excel and lookup is on the same register and related row is not imported jet
     */
    class DXImportLookupException extends \Exception 
    {
        public function __construct($item_id) {
            $message = $item_id;
            $code = 1;
            $previous = null;
            
            parent::__construct($message, $code, $previous);
        }
    }
}
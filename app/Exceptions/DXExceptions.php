<?php
namespace App\Exceptions 
{
    use Symfony\Component\HttpKernel\Exception;
    use Log;
    
    class DXListNotFoundException extends \Exception 
    {
        public function __construct($list_id) {
            $message = "List with ID = " . $list_id . " is not found!";
            $code = 1;
            $previous = null;

            // make sure everything is assigned properly
            parent::__construct($message, $code, $previous);
        }
    }
    
    class DXItemNotFoundException extends \Exception 
    {
        public function __construct($item_id) {
            $message = "Item with ID = " . $item_id . " is not found!";
            $code = 1;
            $previous = null;

            // make sure everything is assigned properly
            parent::__construct($message, $code, $previous);
        }
    }
    
    class DXFormNotFoundException extends \Exception 
    {
        public function __construct($form_id) {
            $message = "Form with ID = " . $form_id . " is not found!";
            $code = 1;
            $previous = null;

            // make sure everything is assigned properly
            parent::__construct($message, $code, $previous);
        }
    }
    
    class DXWrongDropdownSQLException extends \Exception
    {
        public function __construct($sql_rel) {
            $message = "Wrong SQL for dropdown items!";
            $code = 1;
            $previous = null;
            
            Log::info('Wrong dropdown SQL: ' . $sql_rel);
            
            // make sure everything is assigned properly
            parent::__construct($message, $code, $previous);
        }
    }
    
    class DXWrongAutocompleateTableException extends \Exception
    {
        public function __construct($list_id) {
            $message = "No autocompleate table or field record found for the list with ID = " . $list_id . "!";
            $code = 1;
            $previous = null;
            
            // make sure everything is assigned properly
            parent::__construct($message, $code, $previous);
        }
    }
    
    class DXWrongBindedFieldException extends \Exception
    {
        public function __construct($binded_field_id) {
            $message = "No binding record found for the field with ID = " . $binded_field_id . "!";
            $code = 1;
            $previous = null;
            
            // make sure everything is assigned properly
            parent::__construct($message, $code, $previous);
        }
    }
    
    class DXWrongFileFieldException extends \Exception
    {
        public function __construct($file_field_id) {
            $message = "No file fields found for the field with ID = " . $file_field_id . "!";
            $code = 1;
            $previous = null;
            
            // make sure everything is assigned properly
            parent::__construct($message, $code, $previous);
        }
    }
    
    class DXDownloadfileNotFoundException extends \Exception
    {
        public function __construct() {
            $message = "Download file not found!";
            $code = 1;
            $previous = null;
            
            // make sure everything is assigned properly
            parent::__construct($message, $code, $previous);
        }
    }
    
    class DXNoFormFieldFoundException extends \Exception
    {
        public function __construct($form_id) {
            $message = "No field found for the form with ID = " . $form_id . "!";
            $code = 1;
            $previous = null;
            
            // make sure everything is assigned properly
            parent::__construct($message, $code, $previous);
        }
    }
    
    class DXCustomException extends \Exception
    {
        public function __construct($msg) {
            
            if (strlen($msg) == 0)
            {
                $msg = "Neidentificēta sistēmas kļūda! Lūdzu, sazinieties ar sistēmas uzturētāju.";
            }
            
            $message = $msg;
            $code = 1;
            $previous = null;
            
            // make sure everything is assigned properly
            parent::__construct($message, $code, $previous);
        }
    }
    
    class DXBlockException extends \Exception
    {
        public function __construct($msg) {
            
            if (strlen($msg) == 0)
            {
                $msg = "Neidentificēta sistēmas kļūda! Lūdzu, sazinieties ar sistēmas uzturētāju.";
            }
            
            $message = $msg;
            $code = 1;
            $previous = null;
            
            // make sure everything is assigned properly
            parent::__construct($message, $code, $previous);
        }
    }
    
    class DXExtendedException extends \Exception
    {
        private $_title = "Uzmanību";
        public function __construct($msg, $title) {
            
            if (strlen($msg) == 0)
            {
                $msg = "Neidentificēta sistēmas kļūda! Lūdzu, sazinieties ar sistēmas uzturētāju.";
            }
            
            $message = $msg;
            $code = 1;
            $previous = null;
            $this->_title = $title;
            
            // make sure everything is assigned properly
            parent::__construct($message, $code, $previous);
        }
        
        public function getTitle()
        {
            return $this->_title;
        }
    }
}
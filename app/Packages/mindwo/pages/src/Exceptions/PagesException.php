<?php
namespace mindwo\pages\Exceptions;

/**
 * Lapu apstrādes kļūdas klase.
 */
class PagesException extends \Exception
{
    /**
     * Lapu apstrādes kļūdas konstruktors
     * 
     * @param string $msg Kļūdas paziņojums
     */
    public function __construct($msg) {

        if (strlen($msg) == 0)
        {
            $msg = "Neidentificēta sistēmas kļūda! Lūdzu, sazinieties ar sistēmas uzturētāju.";
        }

        $message = $msg;
        $code = 1;
        $previous = null;

        parent::__construct($message, $code, $previous);
    }
}

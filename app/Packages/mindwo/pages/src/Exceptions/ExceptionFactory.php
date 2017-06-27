<?php
namespace mindwo\pages\Exceptions;

use Config;
use Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;

/**
 * Kļūdu apstrādes klase - izveido kļūdu objektus
 */
class ExceptionFactory
{    
    /**
     * Izveido kļūdas tipam atbilstošo apstrādes klasi.
     * Ja neatbalstīts tips, tad atgriež null.
     * 
     * @param type $request POST/GET pieprasījuma objekts
     * @param Exception $e Kļūdas objekts
     * @return \mindwo\pages\Exceptions\exception|null
     */
    public static function BuildException($request, Exception $e) {
        
        foreach(Config::get('mindwo.exceptions') as $exception) {
            if ($e instanceof $exception['class']) {
                $class = "mindwo\\pages\\Exceptions\\Handlers\\" . $exception['handler']; 
                return new $class($request, $e);
            }
        }
        
        return null; // Nav atrasta neviena atblstoša kļūdas klase
    }
}

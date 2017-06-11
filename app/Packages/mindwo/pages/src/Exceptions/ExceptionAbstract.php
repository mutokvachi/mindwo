<?php

namespace mindwo\pages\Exceptions;

/**
 * Kļūdu apstrādes abstraktā klase.
 * Definē visām kļūdu apstrādem kopīgās metodes
 */
abstract class ExceptionAbstract
{

    /**
     * POST/GET pieprasījuma oobjekts
     * 
     * @var Object 
     */
    public $request = null;

    /**
     * Kļūdas objekts
     * 
     * @var Object 
     */
    public $exception = null;

    /**
     * Veic kļūdas apstrādi un atgriež atbildi, kas tiks nosūtīta klientam uz interneta pārlūku
     */
    abstract function render();

    /**
     * Kļūdu apstrādes klases konstruktors
     * 
     * @param Object $request POST/GET pieprasījuma objekts
     * @param Object $e Kļūdas objekts
     */
    public function __construct($request, $e)
    {
        $this->request = $request;
        $this->exception = $e;
    }

}

<?php

namespace mindwo\pages\Exceptions\Handlers;

use Request;
use mindwo\pages\Exceptions\ExceptionAbstract;

/**
 * Apstrādā kļūdu, ja izsaukta neeksistējoša lapa
 */
class ExceptionPageNotFound extends ExceptionAbstract
{
    /**
     * Apstrādā kļūdu, ja izsaukta neeksistējoša lapa.
     * Ajax pieprasījuma gadījumā atgriež JSON rezultātu ar kļūdas aprakstu.
     * Parasta pieprasījuma gadījumā novirza lapu uz sākuma lapu (jābūt definētai route ar nosaukumu home)
     * 
     * @return type
     */
    public function render()
    {
        if ($this->request->ajax()) {
            $msg = $this->exception->getMessage();
            if (strlen($msg) == 0) {
                $msg = "Sistēmas kļūda! AJAX pieprasītais resurss '" . $this->request->path() . "' nav atrodams!";
            }
            return response()->json(['success' => 0, 'error' => $msg], 404);
        }
        else {
            return response()->view('mindwo/pages::errors.404', array(), 404);
        }
    }

}

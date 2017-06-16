<?php

namespace mindwo\pages\Exceptions\Handlers;

use mindwo\pages\Exceptions\ExceptionAbstract;

/**
 * Apstrādā kļūdu, ja beigusies lietotāja sesija
 */
class ExceptionSessionEnded extends ExceptionAbstract
{
    /**
     * Apstrādā kļūdu, ja beigusies lietotāja sesija
     * Ajax pieprasījuma gadījumā atgriež JSON rezultātu ar kļūdas aprakstu.
     * Parasta pieprasījuma gadījumā atgriež autorizācijas lapu (jābūt definēta skatam index)
     * 
     * @return type
     */
    public function render()
    {
        if ($this->request->ajax())
        {
            return response()->json(['success' => 0, 'error' => 'Lietotāja sesija ir beigusies!'], 401);
        }
        else
        {
            return response()->view('mindwo/pages::index', ['error' => 'Lietotāja sesija ir beigusies!'], 401);
        }
    }

}

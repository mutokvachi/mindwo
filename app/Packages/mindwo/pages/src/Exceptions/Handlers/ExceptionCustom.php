<?php

namespace mindwo\pages\Exceptions\Handlers;

use mindwo\pages\Exceptions\ExceptionAbstract;

use Auth;

/**
 * Apstrādā visas ne-sistēmiskās kļūdas, kuras radīja projekta loģika (paziņojumu lietotājiem par nederīgiem datiem, neatļautām darbībām utt)
 */
class ExceptionCustom extends ExceptionAbstract
{

    /**
     * Apstrādā visas ne-sistēmiskās kļūdas, kuras radīja projekta loģika (paziņojumu lietotājiem par nederīgiem datiem, neatļautām darbībām utt)
     * Ajax pieprasījuma gadījumā atgriež JSON rezultātu ar kļūdas aprakstu.
     * Parasta pieprasījuma gadījumā atgriež autorizācijas lapu (jābūt definēta skatam index)
     * 
     * @return type
     */
    public function render()
    {
        if (Auth::check() || $this->request->ajax()) {
            return $this->renderNormalError();
        }
        else {
            return $this->renderFatalError();
        }
    }

    /**
     * Atgriež standarta kļūdu paziņojumu
     * 
     * @return Object Response atbilde ar kļūdas paziņojumu
     */
    private function renderNormalError()
    {
        if ($this->request->ajax()) {
            return response()->json(['success' => 0, 'error' => $this->exception->getMessage()], 500);
        }
        else {
            set_default_view_params();
            set_cms_view_params();
            return response()->view('mindwo/pages::errors.attention', ['page_title' => 'Sistēmas kļūda!', 'message' => $this->exception->getMessage()], 500);
        }
    }

    /**
     * Atgriež fatālas kļūdas paziņojumu - lietotājs nav autorizēts
     * 
     * @return Object Response atbilde ar kļūdas paziņojumu
     */
    private function renderFatalError()
    {

        return response()->view('mindwo/pages::errors.fatal', ['message' => $this->exception->getMessage()], 500);
    }

}

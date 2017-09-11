<?php

namespace mindwo\pages\Exceptions\Handlers;

use Request;
use mindwo\pages\Exceptions\ExceptionAbstract;
use Config;

/**
 * Handles DB related errors
 */
class ExceptionDB extends ExceptionAbstract
{
    /**
     * Process error
     *
     * @return mixed
     */
    public function render()
    {
        $err_txt = $this->exception->getMessage();

        if (strpos($err_txt, 'Duplicate entry') !== false) {
            $err_txt = trans('errors.must_be_uniq');
        }
        else {
            $err_txt = trans('errors.err_db_msg_general');
        }

        $details = "";

        if (Config::get('app.debug', false)) {
            $details =  $this->exception->getMessage();
        }

        if ($this->request->ajax()) {
            return response()->json(['success' => 0, 'error' => $err_txt, 'details' => $details], 500);
        }
        else {
            set_default_view_params();
            set_cms_view_params();
            return response()->view('mindwo/pages::errors.attention', ['page_title' => trans('errors.err_db_msg_title'), 'message' =>$err_txt], 500);
        }
    }

}

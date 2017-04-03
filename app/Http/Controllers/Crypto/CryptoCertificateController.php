<?php

namespace App\Http\Controllers\Crypto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Controlls certificate generation
 */
class CryptoCertificateController extends Controller
{

    /**
     * Returns user's panel view
     * @return \Illuminate\View\View
     */
    public function getUserPanelView()
    {
        return view('pages.crypto.user_panel');
    }
}

<?php

namespace App\Http\Controllers\Crypto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Crypto;

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
        $user = \App\User::find(\Auth::user()->id);

        $cert = $user->cryptoCertificate;


        if ($cert && $cert->public_key && $cert->private_key) {
            $has_cert = true;
        } else {
            $has_cert = false;
        }

        return view('pages.crypto.user_panel', [
            'has_cert' => $has_cert
        ]);
    }

    /**
     * Gets user's certificate
     * @param int $user_id User's ID
     * @return json Response conatinig keys or error message 
     */
    public function getUserCertificate($user_id)
    {
        $is_auth_user = false;

        if (!$user_id || $user_id <= 0) {
            $user_id = \Auth::user()->id;
            $is_auth_user = true;
        }

        $user = \App\User::find($user_id);

        if (!$user) {
            return response()->json(['success' => 0, 'msg' => trans('crypto.e_user_not_exists')]);
        }

        $cert = $user->cryptoCertificate;

        if (!$cert) {
            if ($is_auth_user) {
                $msg = trans('crypto.e_current_user_missing_cert');
            } else {
                $msg = trans('crypto.e_specified_user_missing_cert', ['name' => $user->login_name]);
            }

            return response()->json(['success' => 0, 'msg' => $msg]);
        }

        return response()->json(['success' => 1, 'public_key' => $cert->public_key, 'private_key' => $cert->private_key]);
    }

    /**
     * Saves user's certificate
     * @param Request $request Request's data
     * @return JSON Status of request
     */
    public function saveUserCertificate(Request $request)
    {
        $this->validate($request, [
            'private_key' => 'required',
            'public_key' => 'required',
        ]);

        $user = \App\User::find(\Auth::user()->id);

        // Deletes old master keys
        $masterKeys = $user->cryptoMasterKey();

        if ($masterKeys) {
            $masterKeys->delete();
        }

        $private_key = file_get_contents($request->file('private_key'));
        $public_key = file_get_contents($request->file('public_key'));

        $cert = $user->cryptoCertificate;

        if (!$cert) {
            $cert = new Crypto\Certificate();
            $cert->created_user_id = $user->id;
            $cert->created_time = new \DateTime();
        }

        $cert->private_key = $private_key;
        $cert->public_key = $public_key;
        $cert->modified_user_id = $user->id;
        $cert->modified_time = new \DateTime();

        $cert->user()->associate($user);

        $cert->save();

        return response()->json(['success' => 1]);
    }
}
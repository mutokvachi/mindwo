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
    public function getUserCertificate($user_id, $master_key_group_id)
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

        if ($master_key_group_id) {
            $masterKeyObj = $user->cryptoMasterKey()->find($master_key_group_id);
        }

        if ($masterKeyObj && $masterKeyObj->master_key) {
            $masterKey = base64_encode($masterKeyObj->master_key);
        } else {
            $masterKey = null;
        }

        return response()->json(['success' => 1, 'public_key' => base64_encode($cert->public_key), 'private_key' => base64_encode($cert->private_key), 'master_key' => $masterKey]);
    }

    public function getUserMasterKey($user_id, $master_key_group_id)
    {
        $is_auth_user = false;

        if (!$user_id || $user_id <= 0) {
            $user_id = \Auth::user()->id;
            $is_auth_user = true;
        }

        $user = \App\User::find($user_id);

        $masterKey = $user->cryptoMasterKey()->where('master_key_group_id', $master_key_group_id);

        if (!$user) {
            return response()->json(['success' => 0, 'msg' => trans('crypto.e_user_not_exists')]);
        }
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

    public function saveUserMasterKey(Request $request)
    {
        $this->validate($request, [
            'master_key' => 'required',
            'master_key_group_id' => 'required|integer|exists:dx_crypto_masterkey_groups,id',
            'user_id' => 'required|integer',
        ]);
        
        $user_id = $request->input('user_id');

        // If user is not mentioned then generating key for current user
        if (!$user_id || $user_id <= 0) {
            $user_id = \Auth::user()->id;
        }

        // Gets User
        $user = \App\User::find($user_id);

        if (!$user) {
            return response()->json(['success' => 0]);
        }

        // Gets master keys group ID
        $master_key_group_id = $request->input('master_key_group_id');

        // Retrieves master key as file converted to binary
        $master_key_file = file_get_contents($request->file('master_key'));

        // Try to find master key associated to user with specified master key group
        $masterKey = $user->cryptoMasterKey()->where('master_key_group_id', $master_key_group_id)->first();

        // If user don't have such master key group create a new one
        if (!$masterKey) {
            // Finds master key groups object
            $master_key_group = Crypto\MasterkeyGroup::find($request->input('master_key_group_id'));

            // Creates master key and fill in all required data
            $masterKey = new Crypto\Masterkey();
            $masterKey->created_user_id = $user->id;
            $masterKey->created_time = new \DateTime();

            $masterKey->masterKeyGroup()->associate($master_key_group);
            $masterKey->user()->associate($user);
        }

        // Updates data
        $masterKey->master_key = $master_key_file;
        $masterKey->modified_user_id = $user->id;
        $masterKey->modified_time = new \DateTime();

        $masterKey->save();

        return response()->json(['success' => 1]);
    }
}

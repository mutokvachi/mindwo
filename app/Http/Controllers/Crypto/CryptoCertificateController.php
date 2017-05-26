<?php

namespace App\Http\Controllers\Crypto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Crypto;
use DB;

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
     * Gets user's certificate and master keys
     * @param int $user_id User's ID
     * @param int $master_key_group_id Id of master key group
     * @return json Response containig keys or error message 
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

        $masterKeys = [];

        // If one specified then find specific master key
        if ($master_key_group_id && $master_key_group_id > 0) {
            $masterKeyObj = $user->cryptoMasterKey()->where('master_key_group_id', $master_key_group_id)->first();

            if ($masterKeyObj && $masterKeyObj->master_key) {
                $masterKeys[] = ['id' => $masterKeyObj->master_key_group_id, 'value' => $masterKeyObj->master_key];
            }
        } else if ($master_key_group_id == 0) {
            // If one master key is 0 then retrieve all master key certificates            
            $masterKeyObjArray = $user->cryptoMasterKey;

            foreach ($masterKeyObjArray as $masterKeyObj) {

                if ($masterKeyObj->master_key) {
                    $masterKeys[] = ['id' => $masterKeyObj->master_key_group_id, 'value' => $masterKeyObj->master_key];
                }
            }
        }

        return response()->json(['success' => 1, 'user_id' => $user_id, 'salt' => $cert->salt, 'public_key' => base64_encode($cert->public_key), 'private_key' => base64_encode($cert->private_key), 'master_keys' => $masterKeys]);
    }

    /**
     * Check if there are already master keys to master key group
     * @param int $master_key_group_id Id of master key group
     * @return boolean Result if keys were found
     */
    public function hasExistingKeys($master_key_group_id)
    {
        $otherKeys = Crypto\Masterkey::where('master_key_group_id', $master_key_group_id)->first();

        return response()->json(['success' => 1, 'has_keys' => ($otherKeys ? true : false)]);
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
            'salt' => 'required',
        ]);

        $user = \App\User::find(\Auth::user()->id);

        // Deletes old master keys
        $masterKeys = $user->cryptoMasterKey();

        if ($masterKeys) {
            $masterKeys->delete();
        }

        $private_key = file_get_contents($request->file('private_key'));
        $public_key = file_get_contents($request->file('public_key'));
        $salt = $request->input('salt');

        $cert = $user->cryptoCertificate;

        if (!$cert) {
            $cert = new Crypto\Certificate();
            $cert->created_user_id = $user->id;
            $cert->created_time = new \DateTime();
        }

        $cert->private_key = $private_key;
        $cert->public_key = $public_key;
        $cert->salt = $salt;
        $cert->modified_user_id = $user->id;
        $cert->modified_time = new \DateTime();

        $cert->user()->associate($user);

        $cert->save();

        return response()->json(['success' => 1]);
    }
}

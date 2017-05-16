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

        return response()->json(['success' => 1, 'user_id' => $user_id, 'public_key' => base64_encode($cert->public_key), 'private_key' => base64_encode($cert->private_key), 'master_keys' => $masterKeys]);
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

    /**
     * Prepares data to be recrypted with new master key
     * @param int $masterKeyGroupId Master key groups ID
     * @return array Data which must pe recrypted
     */
    public function prepareRecrypt($masterKeyGroupId)
    {
        $masterKeyGroup = \App\Models\Crypto\MasterkeyGroup::find($masterKeyGroupId);

        if (!$masterKeyGroup) {
            return response()->json(['success' => 0, 'msg' => trans('crypto.e_masterkey_group_not_exists')]);
        }

        \App\Models\Crypto\Cache::where('master_key_group_id', $masterKeyGroupId)
                ->delete();
        
        

        $cryptoFields = $this->retrieveCryptedFieldsByMasterKey($masterKeyGroupId);

        $this->createCryptedDataCache($cryptoFields, $masterKeyGroupId);

        $cachedDataCount = $this->getCachedRowCount($masterKeyGroupId);

        $pendingData = $this->getPendingData($masterKeyGroupId);

        return response()->json(['success' => 1, 'pendingData' => $pendingData, 'cachedDataCount' => $cachedDataCount]);
    }

    /**
     * Retrieves all columns which must be recrypted
     * @param int $masterKeyGroupId Master key groups ID
     * @return type
     */
    private function retrieveCryptedFieldsByMasterKey($masterKeyGroupId)
    {
        $cryptoFields = \DB::table('dx_lists AS l')
                ->selectRaw('l.id list_id, o.db_name as table_name, f.db_name as column_name, case when f.type_id = 12 then 1 else 0 end as is_file')
                ->leftJoin('dx_objects AS o', 'o.id', '=', 'l.object_id')
                ->leftJoin('dx_lists_fields AS f', 'f.list_id', '=', 'l.id')
                ->where('f.is_crypted', 1)
                ->where('l.masterkey_group_id', $masterKeyGroupId)
                ->get();

        return $cryptoFields;
    }

    /**
     * Creates cache of records which must be recrypted
     * @param type $cryptoFields
     * @param int $masterKeyGroupId Master key groups ID
     * @return type
     */
    private function createCryptedDataCache($cryptoFields, $masterKeyGroupId)
    {
        $data_array = array();

        $item_lock = array();

        foreach ($cryptoFields as $cryptoField) {
            $cryptoField->column_name = $this->getFileGuidColumn($cryptoField);

            $data = \DB::table($cryptoField->table_name)
                    ->select('id', $cryptoField->column_name . ' as value')
                    ->whereNotNull($cryptoField->column_name)
                    ->get();

            foreach ($data as $dataRow) {
                if (!$this->isRowInCryptoCache($cryptoField->table_name, $cryptoField->column_name, $dataRow->id)) {
                    $data_array[] = array(
                        'master_key_group_id' => $masterKeyGroupId,
                        'ref_table' => $cryptoField->table_name,
                        'ref_column' => $cryptoField->column_name,
                        'ref_id' => $dataRow->id,
                        'is_file' => $cryptoField->is_file,
                        'old_value' => $dataRow->value
                    );
                }

                if (!\App\Libraries\DBHelper::isItemLockedStatus($cryptoField->list_id, $dataRow->id)) {
                    $item_lock[] = array(
                        'list_id' => $cryptoField->list_id,
                        'item_id' => $dataRow->id,
                        'user_id' => \Auth::user()->id,
                        'locked_time' => date('Y-n-d H:i:s')
                    );
                }
            }
        }

        DB::transaction(function () use ($data_array, $item_lock) {
            \App\Models\Crypto\Cache::insert($data_array);
            \DB::table('dx_locks')->insert($item_lock);
        });

        return $data_array;
    }

    /**
     * Gets file columns guid column from name column
     * @param string $cryptoField
     * @return string File guid column name
     */
    private function getFileGuidColumn($cryptoField)
    {
        if ($cryptoField->is_file == 1) {
            $cryptoField->column_name = str_replace('_name', '_guid', $cryptoField->column_name);
        }

        return $cryptoField->column_name;
    }

    /**
     * Check if data row has been put into crypto cache
     * @param string $ref_table Reference table
     * @param string $ref_column Reference column
     * @param string $ref_id Reference ID
     * @return boolean True of false if data row is in cache
     */
    private function isRowInCryptoCache($ref_table, $ref_column, $ref_id)
    {
        $cryptoCacheData = \App\Models\Crypto\Cache::where('ref_table', $ref_table)
                ->where('ref_column', $ref_column)
                ->where('ref_id', $ref_id)
                ->count();

        return $cryptoCacheData > 0 ? true : false;
    }

    /**
     * Get count of how many data rows has been recrypted
     * @param int $masterKeyGroupId Master key groups ID
     */
    private function getCachedRowCount($masterKeyGroupId)
    {

        \App\Models\Crypto\Cache::where('master_key_group_id', $masterKeyGroupId)
                ->whereNotNull('new_value')
                ->count();
    }

    /**
     * Gets data which must be recrypted with new master key
     * @param int $masterKeyGroupId Master key groups ID
     * @return \App\Models\Crypto\Cache Data to recrypt
     */
    private function getPendingData($masterKeyGroupId)
    {
        return \App\Models\Crypto\Cache::where('master_key_group_id', $masterKeyGroupId)
                        ->whereNull('new_value')
                        ->orderBy('ref_table', 'asc')
                        ->orderBy('ref_column', 'asc')
                        ->orderBy('ref_id', 'asc')
                        ->get();
    }
}
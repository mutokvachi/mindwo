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
     * Checks if there is existing regeneration process
     * @param int $masterKeyGroupId Master key group which is planed to regenerate.
     * @return JSON response of process id if found
     */
    public function checkExistingRegenProcesses($masterKeyGroupId)
    {
        $masterKeyGroup = \App\Models\Crypto\MasterkeyGroup::find($masterKeyGroupId);

        if (!$masterKeyGroup) {
            return response()->json(['success' => 0, 'msg' => trans('crypto.e_masterkey_group_not_exists')]);
        }

        $regen_process = \App\Models\Crypto\Regen::where('master_key_group_id', $masterKeyGroupId)
                ->where('created_user_id', \Auth::user()->id)
                ->first();

        $process_id = $regen_process ? $regen_process->id : 0;

        return response()->json(['success' => 1, 'process_id' => $process_id]);
    }

    /**
     * Prepares data to be recrypted with new master key
     * @param int $regenProcessId Regeneration process ID
     * @param int $masterKeyGroupId Master key groups ID
     * @param boolean $getMasterKey True if needs to retrieve master key
     * @param string $masterKey New master key wrapped with user's certificate
     * @return JSON Data which must pe recrypted
     */
    public function prepareRecrypt($regenProcessId, $masterKeyGroupId, $getMasterKey, $masterKey = null)
    {
        $masterKeyGroup = \App\Models\Crypto\MasterkeyGroup::find($masterKeyGroupId);

        if (!$masterKeyGroup) {
            return response()->json(['success' => 0, 'msg' => trans('crypto.e_masterkey_group_not_exists')]);
        }

        // Deletes old processes if found for current user
        if ($regenProcessId == 0) {
            \App\Models\Crypto\Regen::where('master_key_group_id', $masterKeyGroupId)
                    ->where('created_user_id', \Auth::user()->id)
                    ->delete();

            $process = new \App\Models\Crypto\Regen();
            $process->master_key = $masterKey;
            $process->master_key_group_id = $masterKeyGroupId;
            $process->created_user_id = \Auth::user()->id;
            $process->created_time = new \DateTime();
            $process->modified_user_id = \Auth::user()->id;
            $process->modified_time = new \DateTime();
            $process->save();

            $cryptoFields = $this->retrieveCryptedFieldsByMasterKey($masterKeyGroupId);

            $this->createCryptedDataCache($cryptoFields, $process->id);
        } else {
            $process = \App\Models\Crypto\Regen::find($regenProcessId);

            // If not found creates a new one
            if (!$process) {
                return $this->prepareRecrypt(0, $masterKeyGroupId, $getMasterKey, $masterKey);
            }
        }

        $rowCount = $this->getRecryptRowCount($process->id);

        $pendingData = $this->getPendingData($process->id);

        return response()->json(['success' => 1,
                    'pendingData' => $pendingData,
                    'cachedDataCount' => $rowCount['cachedCount'],
                    'totalDataCount' => $rowCount['totalCount'],
                    'masterKey' => $getMasterKey ? $process->master_key : '',
                    'regenProcessId' => $process->id]);
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
     * @param Array $cryptoFields Fields that must be recrypted
     * @param int $regenProcessId Regeneration process ID
     * @return type
     */
    private function createCryptedDataCache($cryptoFields, $regenProcessId)
    {
        $data_array = array();

        $item_lock = array();

        foreach ($cryptoFields as $cryptoField) {
            // $cryptoField->column_name = $this->getFileGuidColumn($cryptoField);

            if ($cryptoField->is_file == 1) {
                $list = \App\Libraries\DBHelper::getListByTable($cryptoField->table_name);
            } else {
                $list = null;
            }

            $data = \DB::table($cryptoField->table_name)
                    ->select('id', $cryptoField->column_name . ' as value')
                    ->whereNotNull($cryptoField->column_name)
                    ->get();

            $column_name = $this->getFileGuidColumn($cryptoField);

            foreach ($data as $dataRow) {
                if (!$this->isRowInCryptoCache($cryptoField->table_name, $column_name, $dataRow->id)) {
                    $old_value = $this->getOldValue($cryptoField, $dataRow, $list);

                    $data_array[] = array(
                        'regen_id' => $regenProcessId,
                        'ref_table' => $cryptoField->table_name,
                        'ref_column' => $column_name,
                        'ref_id' => $dataRow->id,
                        'is_file' => $cryptoField->is_file,
                        'old_value' => $old_value,
                        'created_user_id' => \Auth::user()->id,
                        'created_time' => new \DateTime(),
                        'modified_user_id' => \Auth::user()->id,
                        'modified_time' => new \DateTime()
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

    private function getOldValue($cryptoField, $dataRow, $list)
    {
        if ($cryptoField->is_file == 1) {
            $old_value = $dataRow->id . '_' . $list->id . '_' . $cryptoField->column_name;
        } else {
            $old_value = $dataRow->value;
        }

        return $old_value;
    }

    /**
     * Gets file columns guid column from name column
     * @param string $cryptoField
     * @return string File guid column name
     */
    private function getFileGuidColumn($cryptoField)
    {
        $column_name = $cryptoField->column_name;

        if ($cryptoField->is_file == 1) {
            $column_name = str_replace('_name', '_guid', $cryptoField->column_name);
        }

        return $column_name;
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
     * @param int $regenProcessId Regeneration process ID
     * @return Array Gets cached and total count of rows which msut be recrypted
     */
    private function getRecryptRowCount($regenProcessId)
    {
        $cachedCount = \App\Models\Crypto\Cache::where('regen_id', $regenProcessId)
                ->whereNotNull('new_value')
                ->count();

        $totalCount = \App\Models\Crypto\Cache::where('regen_id', $regenProcessId)
                ->count();

        return ['cachedCount' => $cachedCount,
            'totalCount' => $totalCount];
    }

    /**
     * Gets data which must be recrypted with new master key
     * @param int $regenProcessId Regeneration process ID
     * @return \App\Models\Crypto\Cache Data to recrypt
     */
    private function getPendingData($regenProcessId)
    {
        $textData = \App\Models\Crypto\Cache::where('regen_id', $regenProcessId)
                        ->whereNull('new_value')
                        ->where('is_file', 0)
                        ->orderBy('ref_table', 'asc')
                        ->orderBy('ref_column', 'asc')
                        ->orderBy('ref_id', 'asc')
                        ->take(1000)
                        ->get();
        
        if($textData && count($textData) > 0){
            return $textData;
        }
        
        $fileData = \App\Models\Crypto\Cache::where('regen_id', $regenProcessId)
                        ->whereNull('new_value')
                        ->where('is_file', 1)
                        ->orderBy('ref_table', 'asc')
                        ->orderBy('ref_column', 'asc')
                        ->orderBy('ref_id', 'asc')
                        ->take(10)
                        ->get();
        
        return $fileData;
    }

    public function saveRegenCache(Request $request)
    {
        $data = $request->all();

        foreach ($data as $cacheId => $newValue) {
            $cacheRecord = \App\Models\Crypto\Cache::find($cacheId);

            if ($cacheRecord->is_file == 0) {
                $cacheRecord->new_value = $newValue;
            } else {
                $cacheRecord->new_value = $this->saveFile($newValue);
            }

            $cacheRecord->modified_user_id = \Auth::user()->id;
            $cacheRecord->modified_time = new \DateTime();

            $cacheRecord->save();
        }

        return response()->json(['success' => 1]);
    }

    private function saveFile($file)
    {
        $document_path = storage_path(config('assets.private_file_path'));

        $file_path = tempnam($document_path, 'cry');

        $file_name = pathinfo($file_path, PATHINFO_FILENAME) . '.' . pathinfo($file_path, PATHINFO_EXTENSION);

        $file->move($document_path, $file_name);

        return $file_name;
    }

    public function applyRegenCache(Request $request)
    {
        $this->validate($request, [
            'regen_process_id' => 'required|exists:dx_crypto_regen,id',
            'master_keys' => 'required'
        ]);

        $process_id = $request->input('regen_process_id');
        $master_keys_wrapped = $request->input('master_keys');

        DB::transaction(function () use ($process_id, $master_keys_wrapped) {
            $process = $this->saveRegen($process_id);

            $this->saveNewMasterKeys($process->master_key_group_id, $master_keys_wrapped);

            DB::table('dx_locks')
                    ->where('user_id', '=', \Auth::user()->id)
                    ->delete();

            $process->delete();
        });

        return response()->json(['success' => 1]);
    }

    private function saveRegen($process_id)
    {
        $process = \App\Models\Crypto\Regen::find($process_id);

        $cacheNotReady = \App\Models\Crypto\Cache::where('regen_id', $process_id)
                ->whereNull('new_value')
                ->count();

        if ($cacheNotReady > 0) {
            throw new \Exceptions\DXCustomException('e_cache_not_ready');
        }

        $cache_rows = $process->cache;

        // Applys all data changes to db
        foreach ($cache_rows as $cache) {
            DB::table($cache->ref_table)
                    ->where('id', $cache->ref_id)
                    ->update([$cache->ref_column => $cache->new_value]);
        }

        return $process;
    }

    private function saveNewMasterKeys($master_key_group_id, $master_keys_wrapped)
    {
        // Finds all users master keys
        $masterKeys = \App\Models\Crypto\Masterkey::where('master_key_group_id', $master_key_group_id)
                ->get();

        if (!$masterKeys) {
            throw new \Exceptions\DXCustomException('e_master_key_not_found');
        }

        foreach ($masterKeys as $masterKey) {
            if (array_key_exists($masterKey->user_id, $master_keys_wrapped)) {
                // Apply master key to user
                $masterKey->master_key = $master_keys_wrapped[$masterKey->user_id];
                $masterKey->save();
            }
        }
    }

    /**
     * Gets all users public keys
     * @return json Response containig keys or error message 
     */
    public function getUserPublicKeys()
    {
        $certs = \App\Models\Crypto\Certificate::all();

        $user_keys = [];

        $counter = 0;

        foreach ($certs as $cert) {
            $user_keys[$counter++] = ['user_id' => $cert->user_id, 'public_key' => base64_encode($cert->public_key)];
        }

        return response()->json(['success' => 1, 'user_keys' => $user_keys]);
    }
}

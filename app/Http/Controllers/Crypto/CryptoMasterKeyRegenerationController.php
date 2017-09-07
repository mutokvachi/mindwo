<?php

namespace App\Http\Controllers\Crypto;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Crypto;
use DB;

/**
 * Controlls master key regenartion and data reencryipton generation
 */
class CryptoMasterKeyRegenerationController extends Controller
{

    /**
     * Checks if there is existing regeneration process
     * @param int $masterKeyGroupId Master key group which is planed to regenerate.
     * @param int $fieldId Field id is specified if process is decrypting or encrypting existing field with data
     * @return JSON response of process id if found
     */
    public function checkExistingRegenProcesses($masterKeyGroupId, $fieldId)
    {
        $masterKeyGroup = \App\Models\Crypto\MasterkeyGroup::find($masterKeyGroupId);

        if (!$masterKeyGroup) {
            return response()->json(['success' => 0, 'msg' => trans('crypto.e_masterkey_group_not_exists')]);
        }

        $other_regen_process = \App\Models\Crypto\Regen::where('master_key_group_id', $masterKeyGroupId)
                ->where('created_user_id', '<>', \Auth::user()->id)
                ->first();

        if ($other_regen_process) {
            return response()->json(['success' => 0, 'msg' => trans('crypto.regen_process_exist_by_user', ['user_name' => $other_regen_process->createdUser->display_name])]);
        }

        $regen_process_query = \App\Models\Crypto\Regen::where('master_key_group_id', $masterKeyGroupId)
                ->where('created_user_id', \Auth::user()->id);

        if($fieldId && $fieldId > 0){
             $regen_process_query->where('field_id', $fieldId);
        }
        
        $regen_process =  $regen_process_query->first();

        $process_id = $regen_process ? $regen_process->id : 0;

        return response()->json(['success' => 1, 'process_id' => $process_id]);
    }
    /**
     * Validates if column in DB is large enough to store encryted data because encrypted data is larger
     *
     * @param int $fieldId Field ID
     * @return void
     */
    public function checkColumnSize($fieldId)
    {
        $field = \App\Models\System\ListField::find($fieldId);

        if(!$field){
            return response()->json(['success' => 0]);
        }

        // For file field we dont need to check size
        if($field->type_id == 12){
            return response()->json(['success' => 1]);
        }

        $table_name = $field->list->object->db_name;

        $con = \DB::connection();
        $column = $con->getDoctrineColumn($table_name, $field->db_name); // \Doctrine\DBAL\Schema\Column

        if(!$column){
            return response()->json(['success' => 0]);
        }

        // Column is text type so it has enough space                  
        if(is_a($column->getType(), 'Doctrine\DBAL\Types\TextType')){
            return response()->json(['success' => 1]);
        }
        
        $db_size = $column->getLength(); // and many other methods - see below

        $needed_size = ($field->max_lenght * 4 + 32);

        if($needed_size > $db_size){
            return response()->json(['success' => 0, 'msg' => trans('crypto.e_encrypt_db_size', ['current' => $db_size, 'needed' => $needed_size])]);
        } else {
            return response()->json(['success' => 1]);
        }

      // 
    }

    /**
     * Prepares data to be recrypted with new master key
     * @param int $regenProcessId Regeneration process ID
     * @param int $masterKeyGroupId Master key groups ID
     * @param boolean $getMasterKey True if needs to retrieve master key
     * @param string $masterKey New master key wrapped with user's certificate
     * @param int $fieldId Optional field id is specified when we decrypt or encrypt specific column. If reencryption then this is 0
     * @return JSON Data which must pe recrypted
     */
    public function prepareRecrypt($regenProcessId, $masterKeyGroupId, $getMasterKey, $masterKey = null, $fieldId = 0)
    {
        $masterKeyGroup = \App\Models\Crypto\MasterkeyGroup::find($masterKeyGroupId);

        if (!$masterKeyGroup) {
            return response()->json(['success' => 0, 'msg' => trans('crypto.e_masterkey_group_not_exists')]);
        }

        if ($regenProcessId == 0) {
            \App\Models\Crypto\Regen::where('master_key_group_id', $masterKeyGroupId)
                    ->where('created_user_id', \Auth::user()->id)
                    ->delete();

            $process = new \App\Models\Crypto\Regen();
            $process->master_key = $masterKey;
            $process->master_key_group_id = $masterKeyGroupId;

            if($fieldId > 0){
                $process->field_id = $fieldId;
            }
            
            $process->created_user_id = \Auth::user()->id;
            $process->created_time = new \DateTime();
            $process->modified_user_id = \Auth::user()->id;
            $process->modified_time = new \DateTime();
            $process->save();

            if($fieldId <= 0){
                $cryptoFields = $this->retrieveCryptedFieldsByMasterKey($masterKeyGroupId);
            } else {
                $cryptoFields = $this->retrieveCryptedFieldsByFieldId($fieldId);
            }

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
                ->selectRaw('o.is_multi_registers, o.id as object_id, l.id list_id, o.db_name as table_name, f.db_name as column_name, case when f.type_id = 12 then 1 else 0 end as is_file')
                ->leftJoin('dx_objects AS o', 'o.id', '=', 'l.object_id')
                ->leftJoin('dx_lists_fields AS f', 'f.list_id', '=', 'l.id')
                ->where('f.is_crypted', 1)
                ->where('l.masterkey_group_id', $masterKeyGroupId)
                ->get();

        return $cryptoFields;
    }

    /**
     * Retrieves field by field id
     * @param int $fieldId Master key groups ID
     * @return type
     */
    private function retrieveCryptedFieldsByFieldId($fieldId)
    {
        $cryptoFields = \DB::table('dx_lists_fields AS f')
                ->selectRaw('o.is_multi_registers, o.id as object_id, l.id list_id, o.db_name as table_name, f.db_name as column_name, case when f.type_id = 12 then 1 else 0 end as is_file')
                ->leftJoin('dx_lists AS l', 'f.list_id', '=', 'l.id')
                ->leftJoin('dx_objects AS o', 'o.id', '=', 'l.object_id')                
                ->where('f.id', $fieldId)
                ->get();

        return $cryptoFields;
    }

    /**
     * If object has multiple lists then limit encrypted/decrypted records by specified list
     *
     * @param object $cryptoField Object contains data about field
     * @param object $query Query which will be executed to retrieve data
     * @return void
     */
    private function limitMultiListRecords($cryptoField, &$query){
        // Check if multi list option is set
        if($cryptoField->is_multi_registers){
            $query->where('multi_list_id', $cryptoField->list_id);      
            return;
        }

        // Check if multiple lists exists for one object
        $listCount = DB::table('dx_lists')->where('object_id', $cryptoField->object_id)->count();

        if($listCount > 1) {
            $this->limitMultiListRecordsByCriteria($cryptoField, $query);
            return;
        }

        return $query;
    }

    /**
     * Limits records which will be encrypted/decrypted if criteria is specified when filtering data shown in register
     *
     * @param object $cryptoField Object contains data about field
     * @param object $query Query which will be executed to retrieve data
     * @return void
     */
    private function limitMultiListRecordsByCriteria($cryptoField, &$query){
        $criteriaList = DB::table('dx_lists_fields AS f')
            ->select('f.criteria', 'f.db_name', 'o.sys_name')
            ->leftJoin('dx_field_operations AS o', 'o.id', '=', 'f.operation_id')
            ->where('list_id', $cryptoField->list_id)
            ->whereNotNull('operation_id')
            ->get();

        foreach($criteriaList as $crit){
            $operator = strtoupper(trim($crit->sys_name));

            $value = $this->prepareValue($operator, $crit->criteria);

            $this->generateWhereClause($query, $crit->db_name, $operator, $value);
        }        
    }

    /**
     * Generates search terms
     * 
     * @param object $query Query which will be executed to retrieve data
     * @param string $columnName Column for where clause
     * @param string $operator Criteria operator
     * @param string $value Value for where clause
     * @return void
     */
    private function generateWhereClause(&$query, $columnName, $operator, $value)
    {
        if ($operator == 'IS NULL' 
            || $operator == 'IS NOT NULL' 
            || $operator == '< NOW()'
            || $operator == '> NOW()') {
            $query->whereRaw($columnName . ' ' . $operator);
        } else {
            $query->whereRaw($columnName . ' ' . $operator . ' ' . $value);
        }
    }

    /**
     * Edit value for specific operator if needed
     * @param string $operator Operator for search
     * @param string $value Value to search for
     * @return string New fixed value
     */
    private function prepareValue($operator, $value)
    {
        if ($operator === "LIKE") {
            $value = '%' . $value . '%';
        }
        if ($operator === "IN" || $operator === "NOT IN") {
            $value = '(' . $value . ')';
        }

        return $value;
    }

    /**
     * Creates cache of records which must be recrypted
     * @param Array $cryptoFields Fields that must be recrypted
     * @param int $regenProcessId Regeneration process ID
     * @return array Cache array
     */
    private function createCryptedDataCache($cryptoFields, $regenProcessId)
    {
        $data_array = array();

        $item_lock = array();

        foreach ($cryptoFields as $cryptoField) {
            if ($cryptoField->is_file == 1) {
                $list = \App\Libraries\DBHelper::getListByTable($cryptoField->table_name);
            } else {
                $list = null;
            }

            $query = \DB::table($cryptoField->table_name)
                    ->select('id', $cryptoField->column_name . ' as value')
                    ->whereNotNull($cryptoField->column_name);

            $this->limitMultiListRecords($cryptoField, $query);  

            $data = $query->get();

            $column_name = $this->getFileGuidColumn($cryptoField);

            foreach ($data as $dataRow) {
                if (!$this->isRowInCryptoCache($cryptoField->table_name, $column_name, $dataRow->id, $regenProcessId)) {
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

    /**
     * Retrieves old value.
     * If it is file then old value will be part of url which is used to downlaod file from javascript.
     * If it is text then just returns same value
     * @param object $cryptoField Inormation about field which will be encrypted
     * @param object $dataRow Contains data of row - value and id
     * @param object $list Lists object for register where value is stored
     * @return satring Value for reencrpytion process - file url or text
     */
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
     * If value is file then gets file columns guid column from name column, else return same column name
     * @param object $cryptoField Inormation about field which will be encrypted
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
     * @param int $regenProcessId Regeneration process ID
     * @return boolean True of false if data row is in cache
     */
    private function isRowInCryptoCache($ref_table, $ref_column, $ref_id, $regenProcessId)
    {
        $cryptoCacheData = \App\Models\Crypto\Cache::where('ref_table', $ref_table)
                ->where('ref_column', $ref_column)
                ->where('ref_id', $ref_id)
                ->where('regen_id', $regenProcessId)
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

        if ($textData && count($textData) > 0) {
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

    /**
     * Saves received reencrypted data into cache which will be later applied
     * @param Request $request Request's data
     * @return JSON respones
     */
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

    /**
     * Saves file with new unique name
     * @param File $file File object
     * @return string File name
     */
    private function saveFile($file)
    {
        $document_path = storage_path(config('assets.private_file_path'));

        $file_path = tempnam($document_path, 'cry');

        $file_name = pathinfo($file_path, PATHINFO_FILENAME) . '.' . pathinfo($file_path, PATHINFO_EXTENSION);

        $file->move($document_path, $file_name);

        return $file_name;
    }

    /**
     * Applies all wrapped master keys and reencrpyted data from cache
     * @param Request $request Request's data
     * @return JSON response
     */
    public function applyRegenCache(Request $request)
    {
        $this->validate($request, [
            'regen_process_id' => 'required|exists:dx_crypto_regen,id'
        ]);

        $process_id = $request->input('regen_process_id');
        $master_keys_wrapped = $request->input('master_keys');
        $fieldId = $request->input('field_id');
        $mode = $request->input('mode');

        DB::transaction(function () use ($process_id, $master_keys_wrapped, $fieldId, $mode) {
            $process = $this->saveRegen($process_id);

            // If they are not set then this is encryption or decryption process (when we change parameter if field is crypted)
            if($master_keys_wrapped && count($master_keys_wrapped) > 0){
                $this->saveNewMasterKeys($process->master_key_group_id, $master_keys_wrapped);
            }

            DB::table('dx_locks')
                    ->where('user_id', '=', \Auth::user()->id)
                    ->delete();

            // Removes all existing regeneration processes becuase it is forbidden to continue them after other process has been already processed
            \App\Models\Crypto\Regen::where('master_key_group_id', $process->master_key_group_id)->delete();

            if($mode > 0 && $fieldId > 0){
                $field = \App\Models\System\ListField::find($fieldId);

                if(!$field){
                    throw new \Exceptions\DXCustomException('e_cache_not_ready');
                }
                
                $field->is_crypted = $mode == 1 ? 1 : 0;
                $field->modified_user_id = \Auth::user()->id;
                $field->modified_time = new \DateTime();
                $field->save();
            }
        });

        return response()->json(['success' => 1]);
    }

    /**
     * Overwrites encrypted data with reencrypted data from cache
     * @param int $process_id Master key regenaration process' id
     * @return \App\Models\Crypto\Regen Process model object
     * @throws \Exceptions\DXCustomException
     */
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

        $dt = new \DateTime();

        // Applys all data changes to db
        foreach ($cache_rows as $cache) {
            DB::table($cache->ref_table)
                    ->where('id', $cache->ref_id)
                    ->update([$cache->ref_column => $cache->new_value,
                    'modified_time' => $dt]);
        }

        return $process;
    }

    /**
     * Saves master keys to users
     * @param int $master_key_group_id Master key's group id
     * @param string $master_keys_wrapped New wrapped master key
     * @throws \Exceptions\DXCustomException
     */
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
     * @param int $master_key_group_id Master key's group id
     * @return json Response containig keys or error message
     */
    public function getUserPublicKeys($master_key_group_id)
    {
        $certs = \App\Models\Crypto\Certificate::all();

        $user_keys = [];

        $counter = 0;

        foreach ($certs as $cert) {
            if ($this->checkUserMasterKey($cert->user_id, $master_key_group_id)) {
                $user_keys[$counter++] = ['user_id' => $cert->user_id, 'public_key' => base64_encode($cert->public_key)];
            }
        }

        return response()->json(['success' => 1, 'user_keys' => $user_keys]);
    }

    /**
     * Check if user has master key for specified group
     * @param int $user_id User Id
     * @param int $master_key_group_id Master key's group id
     * @return boolean Returns true if master key found
     */
    private function checkUserMasterKey($user_id, $master_key_group_id)
    {
        $count = DB::table('dx_crypto_masterkeys')
                ->where('user_id', $user_id)
                ->where('master_key_group_id', $master_key_group_id)
                ->count();

        return $count > 0;
    }

    /**
     * Retrieve master key group by field
     *
     * @param int $field_id Field ID
     * @return JSON Response data
     */
    public function getMasterKeyGroupByField($field_id)
    {
         $field = \App\Models\System\ListField::find($field_id);

         if($field && $field->list && $field->list->masterkey_group_id){
            return response()->json(['success' => 1, 'masterkey_group_id' => $field->list->masterkey_group_id]);
         }
         else{
             return response()->json(['success' => 0]);
         }
    }
}

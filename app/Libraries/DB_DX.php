<?php

namespace App\Libraries
{

    use DB;
    use App\Libraries\DBHistory;
    use Auth;
    use \App\Exceptions;

    /**
     * Prepares history audit information for non-CMS SQL inserts, updates and delete.
     * This class can be used for custom controllers which process data changes
     */
    class DB_DX
    {     
        /**
         * Stores register and data object meta information
         *
         * @var array
         */
        private $list_object = null;

        /**
         * Stores register fields meta information
         *
         * @var array
         */
        private $list_fields = null;

        /**
         * Array where to store WHERE condition used for update and delete statements
         *
         * @var array
         */
        private $arr_where = [];
        
        /**
         * Array with updated fields values
         *
         * @var array
         */
        private $update_arr = [];

        /**
         * DB object for update
         *
         * @var DB
         */
        private $update_obj = null;

        /**
         * Indicates if update() method was called before commitUpdate() call
         *
         * @var boolean
         */
        private $is_update_prepared = false;

        /**
         * History object for update and delete operations - to be used in transaction
         *
         * @var \App\Libraries\DBHistory
         */
        private $update_history_obj = null;

        /**
         * Inits class with register ID as parameter
         *
         * @param [type] $list_id
         * @return \App\Libraries\DB_DX
         */
        public function list($list_id) {
            $this->list_object = DBHelper::getListObject($list_id);

            if (!$this->list_object->is_history_logic) {
                throw new Exceptions\DXCustomException(trans('errors.object_dont_have_history', ['table' => $table_name]));
            }
            $this->list_fields = DBHistory::getListFields($list_id);

            return $this;
        }

        /**
         * Inits class with table name as parameter
         *
         * @param string $table_name Table name where insert/update/delete will happen
         * @return \App\Libraries\DB_DX
         */
        public function table($table_name) {
            $obj = DB::table('dx_objects')->where('db_name', '=', $table_name)->get();
            if (count($obj) != 1) {
                throw new Exceptions\DXCustomException(trans('errors.cant_identify_object', ['table' => $table_name, 'found' => count($obj)]));
            }

            $list = DB::table('dx_lists')->where('object_id', '=', $obj[0]->id)->get();

            if (count($list) != 1) {
                throw new Exceptions\DXCustomException(trans('errors.cant_identify_register', ['table' => $table_name, 'found' => count($obj)]));
            }

            return $this->list($list[0]->id);
        }

        /**
         * Makes SQL insert and returns inserted item ID. Writes audit data as well.
         *
         * @param array $save_arr Data to be inserted as array (key and value pairs)
         * @return integer Inserted item ID
         */
        public function insertGetId($save_arr) {            
            $save_arr = $this->addHistory($save_arr);
           
            $id = DB::table($this->list_object->db_name)->insertGetId($save_arr);

            $history = new DBHistory($this->list_object, null, null, $id);
            $history->makeInsertHistory();

            return $id;
        }

        /**
         * Prepares data for deletion
         *
         * @return \App\Libraries\DB_DX
         */
        public function delete() {
            if (count($this->arr_where) == 0) {
                throw new Exceptions\DXCustomException(trans('errors.object_update_without_where', ['table' => $table_name]));
            }
            
            $id = $this->getIdVal();

            $this->update_history_obj = new DBHistory($this->list_object, $this->list_fields, null, $id);
            $this->update_history_obj->setCurrentData();
            
            return $this;
            
        }

        /**
         * Deletes item from data base and store historical data in audit table
         *
         * @return void
         */
        public function commitDelete() {
            if (!$this->update_history_obj) {
                throw new Exceptions\DXCustomException(trans('errors.object_update_commit_no_prepare'));
            }

            $id = $this->getIdVal();

            $this->update_history_obj->makeDeleteHistory();
            DB::table($this->list_object->db_name)->where('id', '=', $id)->delete();
        }

        /**
         * Set WHERE condition
         *
         * @param string $fld Field name
         * @param string $oper Field operation, for example !=
         * @param string $val Field value
         * @return \App\Libraries\DB_DX
         */
        public function where($fld, $oper, $val) {
            array_push($this->arr_where, ['field' => $fld, 'operation' => $oper, 'value' => $val]);
            return $this;
        }

        /**
         * Prepares data for updating - compares with existing values and store changes in array
         *
         * @param array $save_arr Array with updated fields values
         * @return \App\Libraries\DB_DX
         */
        public function update($save_arr) {
            
            if (count($this->arr_where) == 0) {
                throw new Exceptions\DXCustomException(trans('errors.object_update_without_where', ['table' => $table_name]));
            }

            $id = $this->getIdVal();

            $arr_data = $this->getKeyArr($save_arr);

            $this->update_history_obj = new DBHistory($this->list_object, $this->list_fields, $arr_data, $id);
            $this->update_history_obj->compareChanges();
            
            if ($this->update_history_obj->is_update_change) {
                $this->update_arr = $this->addHistory($save_arr);
                $this->update_obj = DB::table($this->list_object->db_name);
                
                foreach($this->arr_where as $crit) {
                    $this->update_obj->where($crit['field'], $crit['operation'], $crit['value']);
                }
            }

            $this->is_update_prepared = true;

            return $this;
        }

        /**
         * Performs update
         *
         * @return boolean Returns TRUE if an update actualy was done (was changed values) or FALSE otherwise
         */
        public function commitUpdate() {

            if (!$this->is_update_prepared) {
                throw new Exceptions\DXCustomException(trans('errors.object_update_commit_no_prepare'));
            }            

            if ($this->update_obj) {
                $this->update_history_obj->makeUpdateHistory();
                $this->update_obj->update($this->update_arr);

                return true;
            }

            return false;
        }

        /**
         * Clears arrays for next update
         * Used for loops
         *
         * @return void
         */
        public function clearUpdate() {
            $this->is_update_prepared = false;
            $this->update_obj = null;
            $this->update_history_obj = null;
            $this->arr_where = [];
            $this->update_arr = [];
        }

        /**
         * Get ID value from where criterias or throw error
         *
         * @return integer
         */
        private function getIdVal() {
            foreach($this->arr_where as $crit) {
                if (strtolower($crit['field']) === "id") {
                    return $crit['value'];
                }
            }
            
            throw new Exceptions\DXCustomException(trans('errors.object_update_without_id', ['table' => $table_name]));            
        }

        /**
         * Prepares data array for using in history object
         *
         * @param array $save_arr
         * @return void
         */
        private function getKeyArr($save_arr) {
            $arr_data = [];
            foreach($save_arr as $key => $val) {
                $arr_data[":" . $key] = $val;
            }

            return $arr_data;
        }

        /**
         * Appends user info who inserted/modified data in saving array
         *
         * @param array $save_arr Data to be saved as array
         * @return array Data to be saved with appended user info
         */
        private function addHistory($save_arr) {
            $time_now = date('Y-n-d H:i:s');

            $save_arr["created_user_id"] = Auth::user()->id;
            $save_arr["created_time"] = $time_now;
            $save_arr["modified_user_id"] = Auth::user()->id;
            $save_arr["modified_time"] = $time_now;

            return $save_arr;
        }

    }
}
<?php

namespace App\Libraries
{

    use DB;
    use App\Libraries\DBHistory;
    use Auth;
    use \App\Exceptions;

    class DB_DX
    {     

        private $list_object = null;
        private $list_fields = null;

        private $arr_where = [];

        private $update_arr = [];
        private $update_obj = null;

        private $is_update_prepared = false;
        private $update_history_obj = null;

        public function list($list_id) {
            $this->list_object = DBHelper::getListObject($list_id);

            if (!$this->list_object->is_history_logic) {
                throw new Exceptions\DXCustomException(trans('errors.object_dont_have_history', ['table' => $table_name]));
            }
            $this->list_fields = DBHistory::getListFields($list_id);

            return $this;
        }

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

        public function insertGetId($save_arr) {            
            $save_arr = $this->addHistory($save_arr);
           
            $id = DB::table($this->list_object->db_name)->insertGetId($save_arr);

            $history = new DBHistory($this->list_object, null, null, $id);
            $history->makeInsertHistory();

            return $id;
        }

        public function where($fld, $oper, $val) {
            array_push($this->arr_where, ['field' => $fld, 'operation' => $oper, 'value' => $val]);
            return $this;
        }

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

        public function commitUpdate() {

            if (!$this->is_update_prepared) {
                throw new Exceptions\DXCustomException(trans('errors.object_update_commit_no_prepare'));
            }            

            if ($this->update_obj) {
                $this->update_history_obj->makeUpdateHistory();
                $this->update_obj->update($this->update_arr);
            }
        }

        private function getIdVal() {
            foreach($this->arr_where as $crit) {
                if (strtolower($crit['field']) === "id") {
                    return $crit['value'];
                }
            }
            
            throw new Exceptions\DXCustomException(trans('errors.object_update_without_id', ['table' => $table_name]));            
        }

        private function getKeyArr($save_arr) {
            $arr_data = [];
            foreach($save_arr as $key => $val) {
                $arr_data[":" . $key] = $val;
            }

            return $arr_data;
        }

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
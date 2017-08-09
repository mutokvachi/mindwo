<?php

namespace App\Libraries\FieldsImport
{
    use DB;
    use Auth;
    use App\Libraries\DBHistory;
    use App\Exceptions;
    use Config;
    
    /**
     * Field importing from Excel - related record
     */
    class FieldImport_rel_id extends FieldImport
    {    
        /**
         * List Id for worlflow steps which are stored in db table dx_workflows
         */
        const REL_LIST_WF_STEPS = 110;
        
        /**
         * Sets field value
         */
        public function prepareVal()
        {
            $val = $this->excel_value;
            if (strlen($val) == 0)
            {
                $val = null;
            }
            else
            {
                $val = $this->getRelId($val);
                $this->is_val_set = 1;
            }

            $this->val_arr[$this->fld->db_name] = $val;
        }
        
        /**
         * Finds classifier record ID by title field value
         * If record not found then create new
         * 
         * @param string $val Classifier title
         * @return integer Classifier ID
         */
        private function getRelId($val) {
            
            $data_arr = $this->getAutocompleateArray($this->fld->rel_list_id, $this->fld->rel_field_id, $val, $this->fld->field_id);            
            
            if (count($data_arr) > 1) {
                
                if ($this->fld->list_id == self::REL_LIST_WF_STEPS) {
                    if (isset($this->save_arr["list_id"])) {
                        $fld = DB::table('dx_lists_fields')
                                ->where('list_id', '=', $this->save_arr["list_id"])
                                ->where('title_list', '=', $val)
                                ->first();
                        
                        if ($fld) {
                            return $fld->id;
                        }
                        else {
                            $list = DB::table('dx_lists')->where('id', '=', $this->save_arr["list_id"])->first();
                            throw new Exceptions\DXCustomException(trans('errors.import_lookup_no_field', ['fld' => $val, 'list' => $list->list_title]));
                        }                        
                    }
                }
                throw new Exceptions\DXCustomException(trans('errors.import_lookup_several', ['fld' => $this->fld->title_list, 'term' => $val, 'list' => $this->fld->rel_list_title]));
            }
            
            if (count($data_arr) == 1) {
                return $data_arr[0]->id;
            }            
            
            if ($this->fld->list_id == $this->fld->rel_list_id) {
                throw new Exceptions\DXImportLookupException($val);
            }
            
            return $this->insertRel($val);
        }
        
        /**
         * Inserts new classifier record
         * 
         * @param string $val Classifier title
         * @return int Classifier ID
         */
        private function insertRel($val) {
            // Check rights on list
            \App\Libraries\Helper::checkSaveRights($this->fld->rel_list_id, 1);
            
            $arr_val = [];
            
            if ($this->fld->rel_list_id == Config::get('dx.employee_list_id')) {
                // split full name into First name and Last name
                $val_formated = trim(str_replace("  ", " ", $val));
                $arr_names = explode(" ", $val_formated);
                
                if (count($arr_names) == 2) {
                    $arr_val['first_name'] = $arr_names[0];
                    $arr_val['last_name'] = $arr_names[1];                    
                }
                else if (count($arr_names) > 2) {
                    $arr_val['first_name'] = $arr_names[0];
                    $arr_val['last_name'] = $arr_names[1];
                    for ($i=2; $i<count($arr_names); $i++) {
                        $arr_val['last_name'] .= " " . $arr_names[$i];
                    }
                }
                else {
                    throw new Exceptions\DXImportEmployeeNameException($val);
                }
            }
            else {
                $arr_val[$this->fld->rel_field_name] = $val;
            }
            
            if ($this->fld->rel_table_is_history_logic) {
                $time_now = date('Y-n-d H:i:s');
                
                $arr_val["created_user_id"] = Auth::user()->id;
                $arr_val["created_time"] = $time_now;
                $arr_val["modified_user_id"] = Auth::user()->id;
                $arr_val["modified_time"] = $time_now;
            }
            
            $list_object = \App\Libraries\DBHelper::getListObject($this->fld->rel_list_id);
            $id = 0;
            
            DB::transaction(function () use ($arr_val, $list_object, &$id) {
                $id = DB::table($this->fld->rel_table_name)->insertGetId($arr_val);

                $history = new DBHistory($list_object, null, null, $id);
                $history->makeInsertHistory();            
            });
                    
            return $id;
        }
        
        /**
        * Gets array with lookup values
        * 
        * @param integer $list_id      List ID
        * @param integer $txt_field_id Text field ID
        * @param string  $term         Search criteria
        * @return Array  Array with found records 
        * @throws Exceptions\DXCustomException
        */
        private function getAutocompleateArray($list_id, $txt_field_id, $term, $field_id)
        {
            $table_item = DB::table('dx_lists')
                    ->join('dx_objects', 'dx_lists.object_id', '=', 'dx_objects.id')
                    ->select(DB::raw("dx_objects.db_name as table_name, dx_objects.is_multi_registers"))
                    ->where('dx_lists.id', '=', $list_id)
                    ->first();

            $field_item = DB::table('dx_lists_fields')
                    ->select('db_name as rel_field_name', 'is_right_check')
                    ->where('id', '=', $txt_field_id)
                    ->first();

            $main_field_item = DB::table('dx_lists_fields')
                    ->select('is_right_check')
                    ->where('id', '=', $field_id)
                    ->first();

            if (!$table_item || !$field_item || (!$main_field_item && $field_id != -1)) {
                throw new Exceptions\DXCustomException("Sistēmas konfigurācijas kļūda! Uzmeklēšanas laukam nav atrodams reģistrs ar ID " . $list_id . " vai saistītais lauks ar ID " . $txt_field_id . ".");
            }

            $field_item->is_right_check = $main_field_item ? $main_field_item->is_right_check : false; // jo tiesības uzstāda galvenā reģistra laukam bet SQLs tiek veidots no saistītā reģistra

            $rows = DB::select($this->getAutocompleateSQL($table_item, $field_item, $list_id), array($field_item->rel_field_name => $term));

            return $rows;
        }

        /**
         * Prepares SQL statement for lookup fields searching
         * 
         * @param Object  $table_item   Table row
         * @param Object $field_item    Field row
         * @param integer $list_id      List ID
         * @return string   SQL statement
         */
        private function getAutocompleateSQL($table_item, $field_item, $list_id)
        {               
            $sql = getLookupSQL($list_id, $table_item->table_name, $field_item, "txt");

            $sql = $sql . " AND txt = :" . $field_item->rel_field_name . " ORDER BY txt ASC";
            
            return $sql;
        }

    }

}
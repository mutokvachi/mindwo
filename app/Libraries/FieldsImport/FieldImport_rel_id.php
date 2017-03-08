<?php

namespace App\Libraries\FieldsImport
{
    use DB;
    use Auth;
    use App\Libraries\DBHistory;
    use App\Exceptions;
    use Config;
    use Log;
    
    /**
     * Field importing from Excel - related record
     */
    class FieldImport_rel_id extends FieldImport
    {     
        
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
            $data_row = DB::table($this->fld->rel_table_name)
                        ->select('id')
                        ->where($this->fld->rel_field_name, '=', $val)
                        ->first();
            
            if ($data_row) {
                return $data_row->id;
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
            \App\Libraries\Helper::checkSaveRights($this->fld->rel_list_id);
            
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

    }

}
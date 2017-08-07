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
     * Field importing from Excel - multi level record (tree)
     */
    class FieldImport_tree extends FieldImport
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
                        ->get();
            
            if (count($data_row) == 1) {
                return $data_row[0]->id;
            }
            
            $list_title = DB::table('dx_lists')->where('id', '=', $this->fld->rel_list_id)->first()->list_title;
            
            $err_key = "import_wrong_multival";
            if (count($data_row) > 1) {
                $err_key = "import_several_multival";
            }
            
            
            throw new Exceptions\DXCustomException(trans('errors.' . $err_key, ['list' => $list_title, 'val' => $val]));
        }

    }

}
<?php

namespace App\Libraries\DataView\Formatters
{    
    use File;
    
    class Format_file extends FormatAbstract
    {
        /**
        * Formatē lauka vērtību kā datni - formatē vērtību kā HTML saiti ar JavaScript funkciju datnes lejupielādei
        * 
        * @param Array $model_row   Datu modeļa masīva rinda - te pieejami lauka parametri
        * @param Array $data_row    Datu masīva rinda - te lauka vērtība
        * @return void
        */ 
        public function __construct($model_row, $data_row)
        {
            if (strlen($data_row[$model_row["name"]]) > 0)
            {
                $helper = new \App\Libraries\DataView\Helper();
                $this->value = $helper->getFileCell([
                                     'item_id' => $data_row["id"],
                                     'list_id' => $model_row["list_id"],
                                     'field_id' => $model_row["field_id"],
                                     'cell_value' => $data_row[$model_row["name"]],
                                     'is_pdf' => $this->isPDF($data_row[$model_row["name"]]),
                                     'is_crypted' => $model_row["is_crypted"],
                                     'masterkey_group_id' => $model_row["masterkey_group_id"],
                                ]);  
                $this->values['is_html'] = true;
                $this->values['is_link'] = true;
            }
            else
            {
                $this->value = "";
            }
        }
        
        /**
         * Checks if file is in PDF format
         * 
         * @param string $file_name File name
         * @return boolean True - is PDF, False - not PDF
         */
        private function isPDF($file_name) {
            if (!$file_name) {
                return false;
            }
            
            $ext = strtolower(File::extension($file_name));
            
            return ($ext == 'pdf');
        }
    }
}
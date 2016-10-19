<?php

namespace App\Libraries\FieldsImport
{
    use Illuminate\Support\Facades\File;
    use \App\Exceptions;
    use Webpatser\Uuid\Uuid;
     
    /**
     * Field importing from Excel - file
     */
    class FieldImport_file extends FieldImport
    {        
        /**
         * Sets field value
         */
        public function prepareVal() {                                   
            
            $val = $this->excel_value;
            if (strlen($val) == 0)
            {
                $file_guid_name = str_replace("_name", "_guid", $this->fld->db_name);
                        
                $this->val_arr[$file_guid_name] = null;
                $this->val_arr[$this->fld->db_name] = null;            
            }
            else
            {
                $this->saveFile($this->excel_value);                
            }            
        }
        
        /**
         * Save file to CMS storage folder and set's array values with file info
         * 
         * @param string $file_name File name
         * @throws Exceptions\DXCustomException
         */
        private function saveFile($file_name) {
            
            $source_path = $this->tmp_dir . DIRECTORY_SEPARATOR . $file_name;
            
            if (!File::exists($source_path))
            {
                throw new Exceptions\DXCustomException(sprintf(trans('errors.import_zip_file_not_exists'), $file_name));
            }
            
            $target_folder = \App\Libraries\Helper::folderSlash(\App\Http\Controllers\FileController::getFileFolder($this->fld->is_public_file));
            
            //we store files on server with GUID names so they can be unique
            $target_file = Uuid::generate(4) . "." . File::extension($file_name);
            
            if (!File::copy($source_path, $target_folder . $target_file))
            {
                throw new Exceptions\DXCustomException(sprintf(trans('errors.import_zip_file_cant_copy'), $file_name, $target_folder));
            }
            
            $file_guid_name = str_replace("_name", "_guid", $this->fld->db_name);
                        
            $this->val_arr[$file_guid_name] = $target_file;
            $this->val_arr[$this->fld->db_name] = $file_name;
                        
            $this->is_val_set = 1;
        }

    }

}
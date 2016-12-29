<?php

namespace App\Libraries\FieldsHtm
{
    use DB;
    use File;
    
    /**
     * Datnes lauka attēlošanas klase. Attēlo 3 veida laukus: multi datņu pievienošana, attēls vai parasta datne
     */
    class FieldHtm_file extends FieldHtm
    {
        /**
         * Datnes glabājas 2 laukos: _name un _guid
         * Šajā parametrā norāda _guid vērtību
         * @var string 
         */
        public $value_guid = "";
        
        /**
         * Atgriež lauka attēlošanas HTML
         */
        public function getHtm()
        {                                
            $class_exist = (strlen($this->item_value) > 0) ? "exists" : "new";

            if ($this->item_id == 0 && $this->fld_attr->is_multiple_files)
            {
                return view('fields.file_multi', [
                        'item_field' => $this->fld_attr->db_name,
                        'is_required' => $this->fld_attr->is_required,
                        'frm_uniq_id' => $this->frm_uniq_id
                ])->render();
            }           
            
            $view_name = ($this->fld_attr->is_image_file == 1) ? "image" : "file";
            
            return view('fields.' . $view_name, [
                        'item_id' => $this->item_id, 
                        'list_id' => $this->list_id,
                        'field_id' => $this->fld_attr->field_id,
                        'item_field_remove' => str_replace('_name', '_removed', $this->fld_attr->db_name),
                        'file_guid' => $this->value_guid,
                        'item_field' => $this->fld_attr->db_name,
                        'item_value' => $this->item_value,
                        'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                        'class_exist' => $class_exist,
                        'is_required' => $this->fld_attr->is_required,
                        'ext' => $this->getAllowedExt(),
                        'is_pdf' => $this->isPDF($this->item_value)
            ])->render();
        }

        /**
         * Uzstāda noklusēto vērtību jauna ieraksta gadījumā - datnei nav noklusētā vērtība
         */
        protected function setDefaultVal()
        {
        }
        
        /**
         * Returns allowed extensions and mime types seperated by coma
         * @return string Allowed extensions and mime types seperated by coma
         */
        private function getAllowedExt() {
            
            if ($this->fld_attr->is_readonly) {
                return "";
            }
            
            $extensions =   DB::table('dx_files_headers')->where(function($query) {
                                if ($this->fld_attr->is_image_file) {
                                    $query->where('is_img', '=', 1);
                                }
                            })->get();
            
            $ext_txt = "";
            foreach($extensions as $ext) {
               if (strlen($ext_txt) > 0) {
                   $ext_txt .= ",";
               } 
               
               $ext_txt .= "." . $ext->extention . "," . $ext->content_type;
            }
            
            return $ext_txt;
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
<?php

namespace App\Libraries\FieldsHtm
{
    use DB;
    use Auth;
    use App\Exceptions;
    
    /**
     * Uzmeklēšanas lauka attēlošanas klase
     */
    class FieldHtm_autocompleate extends FieldHtm
    {

        /**
         * Atgriež lauka attēlošanas HTML
         */
        public function getHtm()
        {
            $form_url = getListFormURL($this->fld_attr->rel_list_id);
            
            $frm_uniq_id_js = str_replace("-", "_", $this->frm_uniq_id);
            
            if (!($this->item_value > 0))
            {
                $this->item_value = 0;
            }
            
            return  view('fields.autocompleate', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->fld_attr->db_name, 
                    'field_id' => $this->fld_attr->field_id,
                    'item_value' => $this->item_value,
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                    'rel_list_id' => $this->fld_attr->rel_list_id,
                    'rel_field_id' => $this->fld_attr->rel_field_id,
                    'rel_view_id' => $this->fld_attr->rel_view_id,
                    'rel_display_formula_field' => $this->fld_attr->rel_display_formula_field,
                    'txt_display' => $this->getDisplayText(),
                    'is_required' => $this->fld_attr->is_required,
                    'form_url' => $form_url,
                    'frm_uniq_id_js' => $frm_uniq_id_js
            ])->render();
        }
        
        /**
         * Returns textual value of the field
         */
        public function getTxtVal() {
            return $this->getDisplayText();
        }

        /**
         * Uzstāda noklusēto vērtību jauna ieraksta gadījumā
         */
        protected function setDefaultVal()
        {
           if ($this->item_id != 0 || strlen($this->fld_attr->default_value) == 0) {
                return;
            }
            
            if ($this->fld_attr->default_value == "[ME]")
            {
                $this->item_value = Auth::user()->id;
            }
            else
            {
                $tmp_val = $this->fld_attr->default_value;

                if (is_numeric($tmp_val))
                {
                        $this->item_value = (int) $tmp_val;
                }                
            }
        }
        
        /**
         * Atgriež uzmeklēšanas lauka tekstuālo vērtību
         * 
         * @return string Uzmeklēšanas lauka tekstuālā vērtība
         * @throws Exceptions\DXCustomException
         */
        private function getDisplayText()
        {                         
            if ($this->item_value == 0) {
                return "";
            }
            
            $this->fld_attr->is_right_check = 0; // we turn off right check because otherwise wont work CMS for users with default value [ME]
            
            $val_row =  DB::table($this->fld_attr->rel_table_name)
                        ->select($this->fld_attr->rel_field_name . ' as txt')
                        ->where('id', '=', $this->item_value)
                        ->first();

            $txt_display = "";
            if ($val_row) {
                $txt_display = $val_row->txt;
            }

            return $txt_display;
            
        }

    }

}
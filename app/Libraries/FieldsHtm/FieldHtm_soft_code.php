<?php

namespace App\Libraries\FieldsHtm
{

    /**
     * Programmatūras koda lauka attēlošanas klase
     */
    class FieldHtm_soft_code extends FieldHtm
    {

        /**
         * Atgriež lauka attēlošanas HTML
         */
        public function getHtm()
        {
            return view('fields.textarea_code', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->fld_attr->db_name, 
                    'item_value' => $this->item_value,
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode
            ])->render(); 
        }
        
        /**
         * Returns textual value of the field
         */
        public function getTxtVal()
        {
            return $this->item_value;
        }

        /**
         * Uzstāda noklusēto vērtību jauna ieraksta gadījumā
         */
        protected function setDefaultVal()
        {
            if ($this->item_id == 0 && strlen($this->fld_attr->default_value) > 0) {
                $this->item_value = $this->fld_attr->default_value;
            }
        }

    }

}
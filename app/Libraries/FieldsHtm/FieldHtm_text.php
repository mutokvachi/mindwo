<?php

namespace App\Libraries\FieldsHtm
{

    /**
     * Gara teksta lauka attēlošanas klase - attēlo tekstu vairākās rindās
     */
    class FieldHtm_text extends FieldHtm
    {

        /**
         * Atgriež lauka attēlošanas HTML
         */
        public function getHtm()
        {
            return view('fields.textarea', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->fld_attr->db_name, 
                    'item_value' => $this->item_value,
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                    'max_lenght' => $this->fld_attr->max_lenght,
                    'is_crypted' => $this->fld_attr->is_crypted
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
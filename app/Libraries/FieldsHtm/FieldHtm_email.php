<?php

namespace App\Libraries\FieldsHtm
{

    /*
    * Email rendering class
    */
    class FieldHtm_email extends FieldHtm
    {

        /**
         * Returns rendered HMTL
         * @return string
         */
        public function getHtm()
        {
            return view('fields.email', [
                        'frm_uniq_id' => $this->frm_uniq_id,
                        'item_field' => $this->fld_attr->db_name,
                        'item_value' => $this->item_value,
                        'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                        'max_lenght' => $this->fld_attr->max_lenght,
                        'is_required' => $this->fld_attr->is_required,
                        'is_crypted' => $this->fld_attr->is_crypted,
                        'masterkey_group_id' => $this->fld_attr->masterkey_group_id
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
         * Sets default value in case of new record
         */
        protected function setDefaultVal()
        {
            if ($this->item_id == 0 && strlen($this->fld_attr->default_value) > 0) {
                $this->item_value = $this->fld_attr->default_value;
            }
        }

    }

}
<?php

namespace App\Libraries\FieldsHtm
{

    /**
     * Dropdown with custom textual values (without relation to classifier)
     */
    class FieldHtm_rel_txt extends FieldHtm
    {

        /**
         * Returns field rendering HTML
         */
        public function getHtm()
        {
            return view('fields.rel_txt', [
                        'frm_uniq_id' => $this->frm_uniq_id,
                        'item_field' => $this->fld_attr->db_name,
                        'item_value' => $this->item_value,
                        'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                        'items' => explode(";", $this->fld_attr->items),
                        'is_required' => $this->fld_attr->is_required
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
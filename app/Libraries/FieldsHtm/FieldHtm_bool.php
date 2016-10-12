<?php

namespace App\Libraries\FieldsHtm
{

    /**
     * Jā/Nē lauka attēlošanas klase
     */
    class FieldHtm_bool extends FieldHtm
    {

        /**
         * Atgriež lauka attēlošanas HTML
         */
        public function getHtm()
        {
            $sel_yes = "checked='checked'";
            $sel_no = "checked='checked'";
            
            if ($this->item_value == 1)
            {
                $sel_no= "";
            }
            else
            {
                $sel_yes = "";
            }
                                
            return view('fields.bool', [
                    'item_field' => $this->fld_attr->db_name, 
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                    'sel_yes' => $sel_yes,
                    'sel_no' => $sel_no
            ])->render(); 
        }

        /**
         * Uzstāda noklusēto vērtību jauna ieraksta gadījumā
         */
        protected function setDefaultVal()
        {
            if ($this->item_id == 0 && strlen($this->fld_attr->default_value) > 0)
            {
                $this->item_value = $this->fld_attr->default_value;
            }
            
            if (strlen($this->item_value) == 0)
            {    
                $this->item_value = 0;
            }
        }

    }

}
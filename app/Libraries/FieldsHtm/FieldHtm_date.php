<?php

namespace App\Libraries\FieldsHtm
{
    use Config;
    
    /**
     * Datuma lauka attēlošanas klase
     */
    class FieldHtm_date extends FieldHtm
    {

        /**
         * Atgriež lauka attēlošanas HTML
         */
        public function getHtm()
        {
            if ($this->item_id == 0 && $this->fld_attr->is_manual_reg_nr) {
                return "";
            }
           
            return view('fields.datetime', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->fld_attr->db_name, 
                    'item_value' => $this->item_value,
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                    'fld_width' => '130',
                    'tm_format' => Config::get('dx.txt_date_format', 'd.m.Y'),
                    'is_time' => 'false',
                    'is_required' => $this->fld_attr->is_required
            ])->render();
        }

        /**
         * Uzstāda noklusēto vērtību jauna ieraksta gadījumā - šim laukam nav noklusētā vērtība
         */
        protected function setDefaultVal()
        {
        }

    }

}
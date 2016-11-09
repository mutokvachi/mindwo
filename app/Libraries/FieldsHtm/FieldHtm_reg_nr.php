<?php

namespace App\Libraries\FieldsHtm
{

    use DB;
    use Auth;

    /**
     * Reģistrācijas numura attēlošanas klase
     */
    class FieldHtm_reg_nr extends FieldHtm
    {

        /**
         * Atgriež lauka attēlošanas HTML
         */
        public function getHtm()
        {
            if ($this->item_id == 0 && $this->fld_attr->is_manual_reg_nr) {
                return "";
            }

            $reg_state = \App\Http\Controllers\RegisterController::getRegNrState($this->fld_attr, $this->item_value);
            
            return view('fields.regnr', [
                        'frm_uniq_id' => $this->frm_uniq_id,
                        'item_field' => $this->fld_attr->db_name,
                        'item_value' => $this->item_value,
                        'is_disabled' => ($this->fld_attr->is_readonly || $reg_state['reg_fld_editable'] == 0) ? 1 : $this->is_disabled_mode,
                        'max_lenght' => $this->fld_attr->max_lenght,
                        'is_required' => $this->fld_attr->is_required,
                        'is_reg_btn_shown' => $reg_state['reg_btn_shown'],
                        'reg_nr_field_id' => $this->fld_attr->field_id,
                        'list_id' => $this->list_id
            ])->render();
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
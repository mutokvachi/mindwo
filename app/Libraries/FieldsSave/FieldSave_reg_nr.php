<?php

namespace App\Libraries\FieldsSave
{

    class FieldSave_reg_nr extends FieldSave
    {
        /**
         *
         * Formas reģistrācijas numura lauka klase
         * Objekts nodrošina reģistrācijas numura ģenerēšanu/mainīšanu
         */

        /**
         * Apstrādā lauka vērtību
         */
        public function prepareVal()
        {
            if ($this->fld->is_manual_reg_nr) {
                return;
            }

            $val = $this->request->input($this->fld->db_name, '');
            if (strlen($val) == 0) {
                $val = null;
            }

            if ($val != null) {
                \App\Http\Controllers\RegisterController::checkRegNrUnique($this->fld->table_name, $this->fld->db_name, $this->item_id, $val);
            }
            else {
                $val = \App\Http\Controllers\RegisterController::generateRegNr($this->fld->numerator_id, $this->fld->table_name, $this->fld->db_name, $this->item_id);
                $this->txt_arr[$this->fld->db_name] = $val;
            }

            $this->val_arr[$this->fld->db_name] = $val;
            $this->is_val_set = 1;
        }

    }

}
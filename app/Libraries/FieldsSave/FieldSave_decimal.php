<?php

namespace App\Libraries\FieldsSave
{    
    class FieldSave_decimal extends FieldSave
    {
        /**
         *
         * Formas relāciju lauka klase
         * Objekts nodrošina formas lauka vērtību apstrādi relāciju tipa laukiem
         */
        
        /**
         * Apstrādā lauka vērtību
         */
        public function prepareVal()
        {
            $val = $this->request->input($this->fld->db_name, 0);
            $val = str_replace(",", ".", $val);
            $this->is_val_set = 1;

            $this->val_arr[$this->fld->db_name] = $val;
        }

    }

}
<?php

namespace App\Libraries\FieldsSave
{

    class FieldSave_default extends FieldSave
    {
        /**
         *
         * Formas lauka klase
         * Objekts nodrošina formas lauka vērtību apstrādi (visiem lauku tipiem, kuriem nav speciālas apstrādes klases
         */

        /**
         * Apstrādā lauka vērtību
         */
        public function prepareVal()
        {
            $val = $this->request->input($this->fld->db_name, '');
            if (strlen($val) == 0)
            {
                $val = null;
            }
            else
            {
                $this->is_val_set = 1;
            }

            $this->val_arr[$this->fld->db_name] = $val;
        }

    }

}
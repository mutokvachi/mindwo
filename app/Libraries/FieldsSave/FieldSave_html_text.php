<?php

namespace App\Libraries\FieldsSave
{

    class FieldSave_html_text extends FieldSave
    {
        /**
         *
         * Formas HTML lauka klase
         * Objekts nodrošina formas HTML lauka vērtību apstrādi
         */

        /**
         * Apstrādā lauka vērtību - nepieciešamības gadījumā arī iztīra HTML tagus. Iztīrītā vērtība tiks saglabāta atsevišķā laukā, kura nosaukums beidzas ar _dx_clean
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
            
            if ($this->fld->is_clean_html) {
                $this->val_arr[$this->fld->db_name . "_dx_clean"] = html_entity_decode(htmlspecialchars_decode(strip_tags($val)));
            }
        }

    }

}
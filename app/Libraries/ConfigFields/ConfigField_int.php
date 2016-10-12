<?php

namespace App\Libraries\ConfigFields
{

    class ConfigField_int extends ConfigField
    {
        /**
          *
          * Skaitļa veida konfigurācijas parametra vērtības klase
          *
          *
          * Objekts nodrošina konfigurācijas parametra skaitliskas vērtības atgriešanu
          *
         */

        /**
         * Izgūst konfigurācijas parametra skatlisko vērtību
         * 
         * @return string Konfigurācijas parametra skaitliskā vērtība
         */

        public function getConfigValue()
        {
            $val = ($this->config_row->val_integer) ? $this->config_row->val_integer : 0;
            $this->saveInFile($val);
            return $val;
        }

    }

}
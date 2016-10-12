<?php

namespace App\Libraries\ConfigFields
{

    class ConfigField_soft_code extends ConfigField
    {
        /**
          *
          * Skripta veida konfigurācijas parametra vērtības klase
          *
          *
          * Objekts nodrošina konfigurācijas parametra skripta vērtības atgriešanu
          *
         */

        /**
         * Izgūst konfigurācijas parametra skripta vērtību
         * 
         * @return string Konfigurācijas parametra skripta vērtība
         */

        public function getConfigValue()
        {
            $val = ($this->config_row->val_script) ? $this->config_row->val_script : "";
            $this->saveInFile($val);
            return $val;
        }

    }

}
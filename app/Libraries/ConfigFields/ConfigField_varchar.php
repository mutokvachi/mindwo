<?php

namespace App\Libraries\ConfigFields
{

    class ConfigField_varchar extends ConfigField
    {
        /**
          *
          * Teksta veida konfigurācijas parametra vērtības klase
          *
          *
          * Objekts nodrošina konfigurācijas parametra teksta vērtības atgriešanu
          *
         */

        /**
         * Izgūst konfigurācijas parametra tekstuālo vērtību
         * 
         * @return string Konfigurācijas parametra tekstuālā vērtība
         */

        public function getConfigValue()
        {
            $val = ($this->config_row->val_varchar) ? $this->config_row->val_varchar : "";
            $this->saveInFile($val);
            return $val;
        }

    }

}
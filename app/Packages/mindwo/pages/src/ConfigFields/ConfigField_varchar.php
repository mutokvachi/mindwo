<?php

namespace mindwo\pages\ConfigFields
{

    /**
     *
     * Teksta veida konfigurācijas parametra vērtības klase
     *
     *
     * Objekts nodrošina konfigurācijas parametra teksta vērtības atgriešanu
     *
     */
    class ConfigField_varchar extends ConfigField
    {

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
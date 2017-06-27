<?php

namespace mindwo\pages\ConfigFields
{

    /**
     *
     * Faila nosaukuma veida konfigurācijas parametra vērtības klase
     *
     *
     * Objekts nodrošina konfigurācijas parametra faila nosaukuma atgriešanu
     *
     */
    class ConfigField_file extends ConfigField
    {

        /**
         * Izgūst konfigurācijas parametra faila nosaukumu
         * 
         * @return string Konfigurācijas parametra faila nosaukums
         */
        public function getConfigValue()
        {
            $val = ($this->config_row->val_file_guid) ? $this->config_row->val_file_guid : "";
            $this->saveInFile($val);
            return $val;
        }

    }

}
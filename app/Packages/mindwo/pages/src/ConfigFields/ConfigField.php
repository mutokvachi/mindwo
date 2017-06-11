<?php

namespace mindwo\pages\ConfigFields
{
    use Illuminate\Support\Facades\File;
    use Config;
    
    /**
     *
     * Portāla konfigurācijas lauka abstraktā klase
     *
     *
     * Definē visiem konfigurācijas lauku tipiem kopīgās metodes.
     *
     */
    abstract class ConfigField
    {

        public $config_row = null;

        abstract function getConfigValue();

        /**
         * Portāla konfigurācijas lauka konstuktors
         *
         * @param  string $config_row Konfigurācijas iestatījuma rinda no datu bāzes tabulas dx_config 
         * @return void
         */
        public function __construct($config_row)
        {
            $this->config_row = $config_row;
        }        

        /**
         * Saglabā konfigurācijas parametra vērtību cache datnē
         * 
         * @param string $val Parametra vērtība
         */
        public function saveInFile($val)
        {
            $database_name = Config::get('database.connections.' . Config::get('database.default') . '.database') . "_" . getRootForCache();
            $configPath = base_path() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $database_name . '_param_' . $this->config_row->config_name . '.txt';
            File::put($configPath, $val);
        }

    }

}

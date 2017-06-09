<?php

namespace mindwo\pages\ConfigFields
{

    use mindwo\pages\Exceptions\PagesException;
    use DB;
    use Illuminate\Support\Facades\File;
    use Config;
    
    /**
     *
     * Konfigurācijas lauka izveidošanas klase
     *
     *
     * Objekts izveido konfigurācijas lauka klasi
     *
     */
    class ConfigFieldFactory
    {
        /**
         * Konfigurācijas datu pēdējo izmaiņu laiks (kā skaitlis)
         * 
         * @var integer 
         */
        public static $last_modify = 0;
        
        /**
         * Izveido konfigurācijas lauka objektu
         * 
         * @param  string $config_name   Konfigurācijas iestatījuma nosaukums 
         * @return Object                Konfigurācijas lauka objekts
         */
        public static function build_config($config_name)
        {
            $type = "";
            $config_row = DB::table('dx_config')->where('config_name', '=', $config_name)->first();

            try {
                $field_type_row = DB::table('dx_field_types')->where('id', '=', $config_row->field_type_id)->first();
                $type = $field_type_row->sys_name;
            }
            catch (\Exception $e) {
                throw new PagesException("Neeksistējošs konfigurācijas parametrs '" . $config_name . "'!");
            }

            $class = "mindwo\\pages\\ConfigFields\\ConfigField_" . $type;

            if (class_exists($class)) {
                return new $class($config_row);
            }
            else {
                throw new PagesException("Neatbalstīts konfigurācijas parametra lauka tips '" . $type . "'!");
            }
        }
        
        /**
         * Izgūst konfigurācijas parametra vērtību no cache datnes - ja tāda eksistē
         * 
         * @param string $config_name Konfigurācijas parametra nosaukums
         * @return string Konfigurācijas parametra vērtība vai atgriež tekstu [[NOT SET]], ja parametrs vēl nav saglabāts failā
         */
        public static function getConfigFromFile($config_name)
        {
            $database_name = Config::get('database.connections.' . Config::get('database.default') . '.database') . "_" . getRootForCache();
            
            $configPath = base_path() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $database_name . '_param_' . $config_name . '.txt';
                       
            if (File::isFile($configPath)) {
                
                if (ConfigFieldFactory::$last_modify == 0) {
                    ConfigFieldFactory::$last_modify  = strtotime(DB::table('dx_config')->max('modified_time'));
                }
                $file_change = File::lastModified($configPath);
                
                if ($file_change >= ConfigFieldFactory::$last_modify) {
                    return File::get($configPath);
                }
            }

            return "[[NOT SET]]";
        }

    }

}
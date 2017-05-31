<?php
namespace App\Libraries\DataView\Formatters
{
    class FormatFactory
    {
        /**
        * Formatē lauka vērtību atbilstoši tipam. Iestata arī lauka vērtības novietojuma opciju
        * 
        * @param Array $model_row   Datu modeļa masīva rinda - te pieejami lauka parametri
        * @param Array $data_row    Datu masīva rinda - te lauka vērtība
        * @return FormatAbstract Atgriež vērtības formāta objektu
        */ 
        public static function build_field($model_row, $data_row, $is_formula = false) 
        {            
            $type = $model_row['type'];
            $value = $data_row[$model_row['name']];
            
            $class = "App\\Libraries\\DataView\\Formatters\\Format_" . $type;
            if (class_exists($class)) 
            {
                if ($type == "file")
                {
                    return new $class($model_row, $data_row);
                }
                else
                {                    
                    return new $class($value, $is_formula);
                }
            }
            else 
            {
                return new Format_default($value);
            }
        }       
    }
}
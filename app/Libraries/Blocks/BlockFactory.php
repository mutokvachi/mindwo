<?php
namespace App\Libraries\Blocks 
{
    use App\Exceptions;
    use Request;
    use Log;
    
    class BlockFactory
    {
        /**
        *
        * Lapas bloka izveidošanas klase
        *
        *
        * Objekts izveido lapas bloku atbilstoši norādītajam bloka tipam un parametriem
        *
        */
        
        /**
        * Izveido lapas objektu
        * 
        * @param  string $params    Objekta parametri. Lapas HTML var ievietot atslēgas vārdus formātā [[OBJ=...|PARAM1=...|PARAM2=...|PARAM..N=...]]
        * @return Object            Lapas bloka objekts atbilstoši tipam
        */
        public static function build_block($params) 
        {  
            $type = BlockFactory::getObjType($params);
            
            $class = "App\\Libraries\\Blocks\\Block_" . $type;
            
            if (class_exists($class)) 
            {
                $params = trim(str_replace("OBJ=" . $type, "", $params), "|"); // Izņemam OBJ parametru
                return new $class($params);
            }
            else 
            {
                throw new Exceptions\DXCustomException("Neatbalstīts bloka tips '" . $type . "'!");
            }
        }
        
        /**
        * Izgūst bloka objekta veida parametra vērtību
        * Parametrus HTML tekstā norāda formātā PARAMETRS=VĒRTĪBA, parametri atdalīti ar |
        * Parametrus var norādīt jebkādā secībā
        * 
        * @param  string $params    Bloka parametri
        * @return string            Bloka objekta tips
        */
        public static function getObjType($params)
        {
            $dat_arr = explode('|', $params);
            
            foreach ($dat_arr as $item)
            {
                $val_arr = explode('=', $item);
                
                if ($val_arr[0] == "OBJ")
                {
                    return getBlockParamVal($val_arr);
                }
            }
            
            throw new Exceptions\DXCustomException("Bloka parametros (" . $params . ") nav norādīts objekta tips OBJ!");        
        }
    }
}
<?php
namespace App\Libraries\DataView 
{
    use App\Exceptions;
    use Request;
    use Log;
    class DataViewFactory
    {
        public static function build_view($type, $view_id) 
        {
            $session_guid = Request::input('grid_id', '');
            $filter_data = Request::input('filter_data', '');
            
            $class = "App\\Libraries\\DataView\\DataView" . $type;
            if (class_exists($class)) 
            {
                return new $class($view_id, $filter_data, $session_guid);
            }
            else 
            {
                throw new Exceptions\DXCustomException("Neeksistējošs datu skatījuma tips '" . $type . "'!");
            }
        } 
        
        public static function build_view_obj($list_id, $view_id, $session_guid, $is_hidden_in_model) 
        { 
            if ($session_guid && Request::session()->has($session_guid . "_sql")) 
            {
                return new DataViewObjSession($session_guid);
            }
            else 
            {
                return new DataViewObjConstructor($list_id, $view_id, $is_hidden_in_model);
            }
        }
    }
}
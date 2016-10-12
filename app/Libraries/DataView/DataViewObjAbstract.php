<?php
namespace App\Libraries\DataView 
{    
    abstract class DataViewObjAbstract 
    {
        public $view_sql = "";
        public $view_obj = null;        
        public $session_guid = "";
    }
}
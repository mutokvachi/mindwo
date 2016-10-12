<?php

namespace App\Libraries\DataView 
{
    use Request;
    use App\Exceptions;
    
    class DataViewObjSession extends DataViewObjAbstract
    {
        public function __construct($session_guid)
        {                
            try 
            {
                $this->view_obj = unserialize(Request::session()->get($session_guid . "_view"));
                $this->view_sql = Request::session()->get($session_guid . "_sql");
                $this->session_guid = $session_guid;
            }
            catch (\Exception $e)
            {
                throw new Exceptions\DXCustomException("Nav iespējams iegūt skata SQL no sesijas! Sistēmas kļūda: " . $e->getMessage()); // normālā gadījumā te nevajadzētu nonākt, sesiju kontrolē Laravel framework
            }
            
        }
    }
}

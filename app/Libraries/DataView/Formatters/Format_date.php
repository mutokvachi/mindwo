<?php

namespace App\Libraries\DataView\Formatters
{    
    class Format_date extends FormatAbstract
    {
        /**
        * Formatē lauka vērtību kā datumu. Uzstāda, ka vērtība tiek iecentrēta
        * 
        * @param mixed $value Formatējamā vērtība
        * @return void
        */ 
        public function __construct($value)
        {
            if (strlen($value) > 0)
            {
                $phpdate = strtotime( $value );
                $this->value = date( 'd.m.Y', $phpdate );
                $this->align = "center";
            }
        }
    }
}
<?php

namespace App\Libraries\DataView\Formatters
{    
    class Format_default extends FormatAbstract
    {
        /**
        * Noklusētā lauka formatēšana - datu tipiem, kuriem nav definētas formatēšanas klases
        * 
        * @param mixed $value Formatējamā vērtība
        * @return void
        */ 
        public function __construct($value)
        {
            $this->value = $value;
        }
    }
}
<?php

namespace App\Libraries\DataView\Formatters
{    
    class Format_int extends FormatAbstract
    {
        public function __construct($value)
        {
            $this->value = $value;
            $this->align = "right"; 
        }
    }
}

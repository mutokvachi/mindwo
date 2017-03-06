<?php

namespace App\Libraries\DataView\Formatters
{    
    class Format_html_text extends FormatAbstract
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
            $this->values['is_html'] = true;
        }
    }
}
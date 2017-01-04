<?php

namespace App\Libraries\DataView\Formatters
{    
    class Format_bool extends FormatAbstract
    {
        /**
        * Boolean field formatter
        * 
        * @param mixed $value Value to be formated
        * @return void
        */ 
        public function __construct($value)
        {
            if ($value) {
                $this->value = trans('fields.yes');
            }
            else {
                $this->value = trans('fields.no');
            }
        }
    }
}
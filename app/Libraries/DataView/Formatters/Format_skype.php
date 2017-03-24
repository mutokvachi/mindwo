<?php

namespace App\Libraries\DataView\Formatters
{    
    /**
     * Formats field value as Skype link
     */
    class Format_skype extends FormatAbstract
    {
        /**
        * Class constructor
        * 
        * @param mixed $value Skype name to be formated
        * @return void
        */ 
        public function __construct($value)
        {
            $helper = new \App\Libraries\DataView\Helper();
            $this->value = $helper->getSkypeCell([
                                     'cell_value' => $value
                                ]);
            $this->values['is_html'] = true;
            $this->values['is_link'] = true;
        }
    }
}
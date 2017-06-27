<?php

namespace App\Libraries\DataView\Formatters
{    
    /**
     * Formats field value as WhatsApp link
     */
    class Format_whatsapp extends FormatAbstract
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
            $this->value = $helper->getWhatsAppCell([
                                     'cell_value' => $value
                                ]);
            $this->values['is_html'] = true;
            $this->values['is_link'] = true;
        }
    }
}
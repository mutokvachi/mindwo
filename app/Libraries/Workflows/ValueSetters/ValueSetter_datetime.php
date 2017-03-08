<?php

namespace App\Libraries\Workflows\ValueSetters
{
    /**
     * Formats field value - datetime
     */
    class ValueSetter_datetime extends ValueSetter
    {
        /**
         * Formats value
         */
        public function prepareValue()
        {            
            if ($this->val == "[NOW]") {
                $this->val_formated = date('Y-n-d H:i:s');
            }
            else {
                $this->val_formated = $this->val;
            }
        }
    }

}
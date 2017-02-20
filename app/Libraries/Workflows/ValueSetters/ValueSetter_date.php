<?php

namespace App\Libraries\Workflows\ValueSetters
{
    /**
     * Formats field value - date
     */
    class ValueSetter_date extends ValueSetter
    {
        /**
         * Formats value
         */
        public function prepareValue()
        {
            if ($this->val == "[NOW]") {
                $this->val_formated = date('Y-n-d');
            }
            else {
                $this->val_formated = $this->val;
            }
        }
    }

}
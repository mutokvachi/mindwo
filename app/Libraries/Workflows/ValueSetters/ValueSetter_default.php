<?php

namespace App\Libraries\Workflows\ValueSetters
{
    /**
     * Formats field value - default behavior
     */
    class ValueSetter_default extends ValueSetter
    {
        /**
         * Formats value
         */
        public function prepareValue()
        {
            $this->val_formated = $this->val;
        }
    }

}
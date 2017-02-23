<?php

namespace App\Libraries\Workflows\ValueSetters
{    
    use DB;
    
    /**
     * Custom workflow activity
     */
    abstract class ValueSetter
    {
        /**
         * Field value - initial
         * @var mixed 
         */
        public $val = null;  
        
        /**
         * Formated field value
         * @var mixed 
         */
        public $val_formated = null;
                
        /**
         * Field object
         * @var object 
         */
        public $fld = null;     
        
        /**
         * Table name where field value will be set
         * @var string 
         */
        public $table_name = "";
        
        /**
         * ID of the row which will be updated
         * 
         * @var integer 
         */
        public $item_id = 0;
        
        /**
         * Set value
         */
        abstract protected function prepareValue();

       /**
        * Constructor for field value setter
        * 
        * @param integer $item_id Item ID
        * @param string $table_name Name of table which will be udpated
        * @param object $fld Field object
        * @param mixed $val Field value (before formating)
        */
        public function __construct($item_id, $table_name, $fld, $val)
        {
            $this->fld = $fld;            
            $this->val = $val;
            $this->item_id = $item_id;
            $this->table_name = $table_name;
            
            $this->prepareValue();
            $this->setValue();
        }
        
        /**
         * Updates row field with formated value
         */
        private function setValue() {
            DB::table($this->table_name)
            ->where('id', '=', $this->item_id)
            ->update([
                $this->fld->fld_name => $this->val_formated
            ]);
        }
    }

}

<?php

namespace App\Libraries\Workflows\Activities
{    
    /**
     * Custom workflow activity
     */
    abstract class Activity
    {
        /**
         * Workflow document ID
         * @var integer 
         */
        public $item_id = 0;        
                
        /**
         * Workflow document list ID
         * @var integer 
         */
        public $list_id = 0;     
               
        /**
         * Activity logic
         */
        abstract protected function performActivity();

        /**
         * Constructor for custom workflow activity
         * 
         * @param integer $item_id Document item ID
         * @param integer $list_id Document list ID
         */
        public function __construct($item_id, $list_id)
        {
            $this->item_id = $item_id;            
            $this->list_id = $list_id;
            
            return $this->performActivity();
        }
    }

}

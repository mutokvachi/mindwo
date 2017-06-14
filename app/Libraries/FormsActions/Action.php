<?php

namespace App\Libraries\FormsActions
{    
    use DB;
    
    /**
     * Custom form's action
     */
    abstract class Action
    {
        /**
         * Database table names for which register action is intended
         * If array is empty - action in intended for any database table
         * 
         * @var Array 
         */
        public $db_table_name = [];
        
        /**
         * POST request object 
         * @var \Illuminate\Http\Request 
         */
        public $request = null;     
        
        /**
         * Item ID
         * @var integer 
         */
        public $item_id = 0;
        
        /**
         * List ID
         * @var integer 
         */
        public $list_id = 0;
        
        /**
         * List item edit form ID (expected to be in request params)
         * 
         * @var integer 
         */
        public $form_id = 0;
        
        /**
         * Action logic
         */
        abstract protected function performAction();
        
        /**
         * Sets db_table_name parameter
         */
        abstract protected function setTableName();

        /**
         * Constructor for custom workflow activity
         * 
         * @param \Illuminate\Http\Request $request POST request object 
         * @param integer $item_id Item ID. 0 if item not saved jet
         */
        public function __construct($request, $item_id)
        {
            $this->request = $request;
            $this->item_id = $item_id;
            
            $this->form_id = $request->input("edit_form_id"); // we do not validate here, because it should be validated in controller        
            $this->list_id = DB::table('dx_forms')->where('id', '=', $this->form_id)->first()->list_id;
            
            $this->setTableName();
            $this->validateTableName();
            
            return $this->performAction();
        }
        
        /**
         * Validates if action is executed on correct db object/table
         * Actions can be configured in CMS for any register - so we need to ensure that it was done right
         * 
         * @throws Exceptions\DXCustomException
         */
        private function validateTableName() {
            
            if (!count($this->db_table_name)) {
                return;
            }
            
            $obj = \App\Libraries\DBHelper::getListObject($this->list_id);
            
            foreach($this->db_table_name as $tbl) {
                if ($tbl == $obj->db_name) {
                    return;
                }
            }
            
            throw new Exceptions\DXCustomException(trans('errors.wrong_action_object'));
            
        }
    }

}

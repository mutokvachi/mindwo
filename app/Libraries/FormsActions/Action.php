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
            
            return $this->performAction();
        }
    }

}

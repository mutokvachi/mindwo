<?php

namespace App\Http\Controllers\Calendar\Validators
{

    use DB;

    /**
     * Education groups publishing validator
     */
    abstract class Validator
    {
        /**
         * Group row (from table edu_subjects_groups)
         * @var array
         */
        public $group = null;
        
        /**
         * Validation error text
         * @var string
         */
        public $err_txt = "";
        
        /**
         * Array with validation errors meta data
         * 
         * @var array 
         */
        private $arr_data = [];
        
        /**
         * Validates group
         */
        abstract protected function validateGroup();

        /**
         * Validator constructor
         * 
         * @param array $group Group row (from table edu_subjects_groups)
         */
        public function __construct($group)
        {
            $this->group = $group;
            
            $validator_code = str_replace("App\Http\Controllers\Calendar\Validators\Validator_", "", get_class($this));
            
            $this->err_txt = DB::table('edu_publish_validators')
                             ->where('code', '=', $validator_code)
                             ->first()
                             ->title;
            
            $this->validateGroup();
        }
        
        /**
         * Adds error metda data into array
         * 
         * @param integer $list_id Register ID where is problem
         * @param integer $item_id Item ID for problematic record
         * @param string $title Item title for problematic record - used to display in UI
         * @param string $url Optional URL to where navigate in order to solve an issue
         */
        public function setError($list_id, $item_id, $title, $url = '') {
            array_push($this->arr_data, [
                'list_id' => $list_id, 
                'item_id' => $item_id, 
                'title' => $title,
                'err_text' => $this->err_txt,
                'url' => $url
            ]);
        }
        
        /**
         * Returns errors array
         * 
         * @return array Array with keys: list_id, item_id, title
         */
        public function getErrors() {
            return $this->arr_data;
        }

    }

}

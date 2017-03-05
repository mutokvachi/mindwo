<?php
namespace App\Libraries\DataView {
    
    use Config;
    use App\Exceptions;
    use Webpatser\Uuid\Uuid;
    use App\Http\Controllers\FileController;
    use DB;
    use Illuminate\Support\Facades\File;
    use Auth;
    
    /**
     * Returns view data in JSON format used for RESTfull API service
     */
    class DataViewJSON extends DataView {
        
        /**
         * Constructs view class to get data in JSON format
         * 
         * @param integer $view_id View ID
         * @param string $filter_data Filtering data in JSON format
         * @param string $session_guid Session ID
         */
        public function __construct($view_id, $filter_data, $session_guid)
        {
            $this->initObjects($view_id, $filter_data, $session_guid, 0);
        }
        
        /**
        * Returns view data in JSON format
        * 
        * @return JSON Array with view data in JSON format
        */
        public function getViewHtml() 
        {
            return json_encode($this->getViewDataArray());
        }
        
        /**
         * SQL part for OrderBy - we use default view's ordering here
         * @return string
         */
        protected function getSortingSQL() 
        {
            return "";
        }

        /**
         * SQL part for rows pagination
         * @return string
         */     
        protected function getLimitSQL() 
        {            
            return " LIMIT 0, 100";
        }
    }
}
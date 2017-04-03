<?php

namespace App\Libraries\View\Helpers
{
    use DB;
    
    /**
     * Makes WHERE SQL for field operation - all subordinates (recursive all sub-levels)
     */
    class Subordinates
    {
        /**
         * Array with all employees (table dx_users)
         * @var Array
         */
        private $users = null;
        
        /**
         * Subordinates helper class constructor
         */
        public function __construct()
        {            
            $this->users = DB::table('dx_users')->select('id', 'manager_id')->orderBy('manager_id')->get();
        }
        
        /**
         * Recursively returns all subordinates IDs as string, seperated by coma
         * @param integer $user_id Employee ID for which to get subordinates
         * 
         * @return string All subordinates IDs as string, seperated by coma
         */
        public function getAllSubordinates($user_id) {            
            $ids = "";
            
            foreach($this->users as $user) {
                if ($user->manager_id == $user_id) {
                    
                    if (strlen($ids) > 0) {
                        $ids .= ",";
                    }
                    $ids .= $user->id;
                    $child_ids = $this->getAllSubordinates($user->id);

                    if (strlen($child_ids) > 0 ) {
                        $ids .= "," . $child_ids;
                    }
                }
                
                if ($user->manager_id > $user_id) {
                    break; // no more with this manager_id because we ordered users by manager id
                }                
            }
            
            return $ids;
        }
    }

}
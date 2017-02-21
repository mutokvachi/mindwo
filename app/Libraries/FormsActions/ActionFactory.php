<?php

namespace App\Libraries\FormsActions
{

    use App\Exceptions;
    
    /**
     * Custom form's action factory class
     */
    class ActionFactory
    {
        /**
         * Builds action class
         * 
         * @param \Illuminate\Http\Request $request POST request object 
         * @param integer $item_id Item ID. 0 if item not saved jet
         * @param string $action_code Action code
         * @return \App\Libraries\FormsActions\class
         * @throws Exceptions\DXCustomException
         */
        public static function build_action($request, $item_id, $action_code)
        {
            $class = "App\\Libraries\\FormsActions\\Action_" . $action_code;
            
            if (class_exists($class)) {
                return new $class($request, $item_id);
            }
            else {
                throw new Exceptions\DXCustomException(sprintf(trans('errors.unsuported_action_code'), $action_code));
            }
        }
        
    }

}
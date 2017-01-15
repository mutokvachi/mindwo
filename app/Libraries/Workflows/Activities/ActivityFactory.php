<?php

namespace App\Libraries\Workflows\Activities
{

    use App\Exceptions;
    
    /**
     * Custom workflow activity factory class
     */
    class ActivityFactory
    {
        
        public static function build_activity($item_id, $list_id, $activity_code)
        {
            $class = "App\\Libraries\\Workflows\\Activities\\Activity_" . $activity_code;
            
            if (class_exists($class)) {
                return new $class($item_id, $list_id);
            }
            else {
                throw new Exceptions\DXCustomException(sprintf(trans('workflow.err_unsuported_activity'), $activity_code));
            }
        }
        
    }

}
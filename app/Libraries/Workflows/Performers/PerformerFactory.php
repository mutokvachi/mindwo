<?php

namespace App\Libraries\Workflows\Performers
{

    use App\Exceptions;
    
    /**
     * Darbplūsmas izpildītāja izveidošanas klase
     */
    class PerformerFactory
    {
        /**
         * Izveido izpildītāja objektu
         * 
         * @param  Object  $step_row   Darbplūsmas solis
         * @param  integer $item_id    Ieraksta ID
         * @param  integer $wf_info_id Darbplūsmas instances ID
         * 
         * @return Object             Darbplūsmas izpildītāja objekts
         */
        public static function build_performer($step_row, $item_id, $wf_info_id)
        {
            $class = "App\\Libraries\\Workflows\\Performers\\Performer_" . $step_row->perform_code;
            
            if (class_exists($class)) {
                return new $class($step_row, $item_id, $wf_info_id);
            }
            else {
                throw new Exceptions\DXCustomException("Neatbalstīts darbplūsmas izpildītāja veids '" . $step_row->perform_code . "'!");
            }
        }
        
    }

}
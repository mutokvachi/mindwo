<?php

namespace App\Libraries\Workflows\Performers
{
    use DB;
    use App\Exceptions;
    
    /**
     * Darbplūsmas izpildītājs - dokumenta sagatavotāja departamenta vadītājs
     */
    class Performer_DEP_LEADER extends Performer
    {

        /**
         * Dokumenta izveidotājs (no tabulas dx_field_represent)
         */
        const REPRESENT_CREATOR = 8;

        /**
         * Uzstāda darbinieka ID
         */
        public function setEmployeeID()
        {            
            $fld_row = DB::table("dx_lists_fields")->where('id', '=', $this->step_row->field_id)->first();
            
            \App\Libraries\Workflows\Helper::validateEmplField($fld_row);
            
            $empl_id = \App\Libraries\Workflows\Helper::getDocEmplValue($this->step_row->list_id, $this->item_id, $fld_row);
            
            // nosakam struktūrvienību
            $user = DB::table('dx_users')->where('id', '=', $empl_id)->first();
            
            if (!$user->department_id) {
                throw new Exceptions\DXCustomException("Darbiniekam '" . $user->display_name . "' nav norādīta struktūrvienība!");
            }
            
            // nosakam departamentu
            $dep_id = $this->getDepartmentID($user->department_id);
            
            // nosakam departamenta vadītāju
            $leader = DB::table('dx_users')
                      ->where('is_leader', '=', 1)
                      ->where('department_id', '=', $dep_id)
                      ->first();
            
            if (!$leader) {
                $dep = DB::table('in_departments')->where('id', '=', $user->department_id)->first();
                throw new Exceptions\DXCustomException("Struktūrvienībai '" . $dep->title . "' nav norādīts departamenta vadītājs!");
            }
            
            $item = ["empl_id" => $leader->id, 'due_days' => $this->step_row->term_days, 'wf_approv_id' => null];
            array_push($this->employees, $item);
        }
        
        /**
         * Atgriež departamenta ID
         * 
         * @param integer $struct_id Struktūrvienības ID
         * @return type
         */
        private function getDepartmentID($struct_id) {
            $struct = DB::table('in_departments')
                      ->where('id', '=', $struct_id)
                      ->first();
            
            if (!$struct->parent_id) {
                return $struct->id;
            }
            
            // Visām 1 departamenta struktūrvienībām ir vienāds source_id
            // Departamentam nav parenta ieraksta
            $dep = DB::table('in_departments')
                   ->whereNull('parent_id')
                   ->where('source_id', '=', $struct->source_id)
                   ->first();
            
            return $dep->id;
        }

    }

}
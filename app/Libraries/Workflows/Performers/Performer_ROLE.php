<?php

namespace App\Libraries\Workflows\Performers
{

    use DB;
    use App\Exceptions;
    
    /**
     * Darbplūsmas izpildītājs - lomas lietotājs klase
     */
    class Performer_ROLE extends Performer
    {

        /**
         * Uzstāda darbinieka ID
         */
        public function setEmployeeID()
        {
            $role_row = DB::table('dx_users_roles')
                        ->where('role_id', '=', $this->step_row->role_id)
                        ->first();
            
            if (!$role_row) {
                throw new Exceptions\DXCustomException("Nav iespējams izveidot uzdevumu, jo lomai '" . $this->step_row->role_title . "' nav pievienots neviens darbinieks!");
            }
            
            $item = ["empl_id" => $role_row->user_id, 'due_days' => $this->step_row->term_days, 'wf_approv_id' => null];
            array_push($this->employees, $item);
        }

    }

}
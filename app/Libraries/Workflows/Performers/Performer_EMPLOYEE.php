<?php

namespace App\Libraries\Workflows\Performers
{

    /**
     * Darbplūsmas izpildītājs - tiešais vadītājs klase
     */
    class Performer_EMPLOYEE extends Performer
    {

        /**
         * Uzstāda darbinieka ID
         */
        public function setEmployeeID()
        {
            $item = ["empl_id" => $this->step_row->employee_id, 'due_days' => $this->step_row->term_days, 'wf_approv_id' => null];
            array_push($this->employees, $item);
        }

    }

}
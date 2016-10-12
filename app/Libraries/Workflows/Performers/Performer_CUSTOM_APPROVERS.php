<?php

namespace App\Libraries\Workflows\Performers
{
    use DB;
    
    /**
     * Darbplūsmas izpildītājs - manuāli iestatīti saskaņotāji
     */
    class Performer_CUSTOM_APPROVERS extends Performer
    {

        /**
         * Uzstāda saskaņototāju datus
         */
        public function setEmployeeID()
        {            
            $approvers = DB::table('dx_workflows_approve')
                        ->where('workflow_info_id', '=', $this->wf_info_id)
                        ->where('is_done', '=', 0)
                        ->orderBy('order_index');
            
            $wf_info = DB::table('dx_workflows_info')->where('id', '=', $this->wf_info_id)->first();
            
            if (!$wf_info->is_paralel_approve) {
                $approvers = $approvers->take(1);
            }
             
            $approvers = $approvers->get();            
            
            foreach($approvers as $approver) {
                $item = ["empl_id" => $approver->approver_id, 'due_days' => $approver->due_days, 'wf_approv_id' => $approver->id];
                array_push($this->employees, $item);                
            }
        }

    }

}
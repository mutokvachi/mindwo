<?php

namespace App\Libraries\FormsActions
{    
    /**
     * Starts workflow after items first save
     */
    class Action_START_WORKFLOW extends Action
    {
        /**
         * Performs action
         */
        public function performAction()
        {
            if ($this->request->input('item_id', 0) == 0) {
                $wf = new \App\Http\Controllers\TasksController();
                $wf->startWorkflow($this->list_id, $this->item_id, false, "");
            }
        }

    }

}
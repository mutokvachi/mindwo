<?php

namespace App\Libraries\Blocks\TaskListViews
{    
    use Auth;
    
    /**
     * Tasks view - ACTUAL
     */
    class TaskListView_MY_ACTUAL extends TaskListView
    {
        /**
         * Sets tasks view where criteria
         */
        public function setCriteria() {
            
            $this->rows
                    ->where('t.task_employee_id', '=', Auth::user()->id)
                    ->whereNull('t.task_closed_time');
            
        }

    }

}
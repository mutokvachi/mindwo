<?php

namespace App\Libraries\Blocks\TaskListViews
{    
    use Auth;
    
    /**
     * Tasks view - DONE
     */
    class TaskListView_MY_DONE extends TaskListView
    {
        /**
         * Sets tasks view where criteria
         */
        public function setCriteria() {
            
            $this->rows
                    ->where('t.task_employee_id', '=', Auth::user()->id)
                    ->whereNotNull('t.task_closed_time');
            
        }

    }

}
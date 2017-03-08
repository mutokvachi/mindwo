<?php

namespace App\Libraries\Blocks\TaskListViews
{    
    use Auth;
    
    /**
     * Tasks view - Due today
     */
    class TaskListView_MY_DUETODAY extends TaskListView
    {
        /**
         * Sets tasks view where criteria
         */
        public function setCriteria() {
            
            $this->rows
                    ->where('t.task_employee_id', '=', Auth::user()->id)
                    ->whereNull('t.task_closed_time')
                    ->whereRaw('DATEDIFF(t.due_date, now())=0');
            
        }

    }

}
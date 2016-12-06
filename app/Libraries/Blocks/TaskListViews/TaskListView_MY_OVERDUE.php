<?php

namespace App\Libraries\Blocks\TaskListViews
{    
    use Auth;
    
    /**
     * Accrual start period - Day
     */
    class TaskListView_MY_OVERDUE extends TaskListView
    {
        /**
         * Returns accrual level start date
         * 
         * @return DateTime
         */
        public function setCriteria() {
            
            $this->rows
                    ->where('t.task_employee_id', '=', Auth::user()->id)
                    ->whereNull('t.task_closed_time')
                    ->whereRaw('DATEDIFF(t.due_date, now())<0');
            
        }

    }

}
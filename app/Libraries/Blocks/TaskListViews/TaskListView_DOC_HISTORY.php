<?php

namespace App\Libraries\Blocks\TaskListViews
{     
    /**
     * Tasks view - all non-informative tasks for given list item 
     */
    class TaskListView_DOC_HISTORY extends TaskListView
    {
        /**
         * List ID
         * @var integer 
         */
        public $list_id = 0;
        
        /**
         * Item ID
         * @var integer 
         */
        public $item_id =0;
        
        /**
         * Sets tasks view where criteria
         */
        public function setCriteria() {
            
            $this->rows
                    ->where('t.list_id', '=', $this->list_id)
                    ->where('t.item_id', '=', $this->item_id)
                    ->where('t.task_type_id', '!=', \App\Http\Controllers\TasksController::TASK_TYPE_INFO);
            
        }

    }

}
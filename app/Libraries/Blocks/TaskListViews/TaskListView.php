<?php

namespace App\Libraries\Blocks\TaskListViews
{
    use DB;
    
    /**
     * Class for task widget views data rendering
     */
    abstract class TaskListView
    {        
        /**
         * Tasks data rows
         * @var object
         */
        public $rows = null;
                
        /**
         * Set where criteria for data rows query
         */
        abstract protected function setCriteria();

        /**
         * Task view constructor
         *  
         * @return void
         */
        public function __construct()
        {
            $this->rows = DB::table('dx_tasks as t')
                          ->select(
                                  't.id',
                                  'l.list_title',
                                  't.item_reg_nr',
                                  't.item_info',
                                  'tt.title as task_type',
                                  't.item_id',
                                  't.list_id',
                                  't.due_date',
                                  't.task_type_id',
                                  't.task_closed_time',
                                  't.task_details',
                                  DB::raw('DATEDIFF(t.due_date, now()) as days_left'),
                                  'ts.title as task_status',
                                  't.task_status_id',
                                  't.item_empl_id',
                                  'u.display_name as item_employee',
                                  'u.picture_guid',
                                  'te.display_name as performer_employee',
                                  'te.picture_guid as performer_picture',
                                  't.task_comment',
                                  't.task_created_time',
                                  't.task_employee_id',
                                  DB::raw('exists(select id from dx_tasks as ht where ht.list_id = t.list_id and ht.item_id = t.item_id and ht.id != t.id) as is_history'),
                                  't.wf_info_id',
                                  'wd.is_any_delegate'
                          )
                          ->leftJoin('dx_lists as l', 't.list_id', '=', 'l.id')
                          ->leftJoin('dx_tasks_types as tt', 't.task_type_id', '=', 'tt.id')
                          ->leftJoin('dx_tasks_statuses as ts', 't.task_status_id', '=', 'ts.id')
                          ->leftJoin('dx_users as u', 't.item_empl_id', '=', 'u.id')
                          ->leftJoin('dx_users as te', 't.task_employee_id', '=', 'te.id')
                          ->leftJoin('dx_workflows_info as wi', 't.wf_info_id', '=', 'wi.id')
                          ->leftJoin('dx_workflows_def as wd', 'wi.workflow_def_id', '=', 'wd.id');
        }
        
        /**
         * Returns tasks data rows
         */
        public function getRows() {
            $this->setCriteria();
            $this->rows->orderBy('t.id', 'DESC');
            return $this->rows->get();
        }

    }

}

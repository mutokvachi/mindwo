<?php

namespace App\Libraries\FormsActions
{

    use DB;

    /**
     * Starts workflow (from another register but based on the same db table) after items first save
     * Intended for any db object/list
     */
    class Action_START_WORKFLOW_RELATED extends Action
    {

        /**
         * Sets db_table_name parameter
         */
        public function setTableName()
        {
            // action if for any table
        }

        /**
         * Performs action
         */
        public function performAction()
        {
            if ($this->request->input('item_id', 0) == 0) {
                $obj = \App\Libraries\DBHelper::getListObject($this->list_id);

                $rel_list = DB::table('dx_lists as l')
                        ->where('l.object_id', '=', $obj->id)
                        ->where('l.id', '!=', $this->list_id)
                        ->whereExists(function ($query)
                        {
                            $query->select(DB::raw(1))
                            ->from('dx_workflows_def as wf')
                            ->whereNull('wf.valid_to')
                            ->whereRaw('wf.list_id = l.id');
                        })
                        ->first();

                $wf = new \App\Http\Controllers\TasksController();
                $wf->startWorkflow($rel_list->id, $this->item_id, false, "");
            }
        }

    }

}
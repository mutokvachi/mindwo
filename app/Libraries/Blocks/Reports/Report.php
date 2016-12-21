<?php

namespace App\Libraries\Blocks\Reports;

use DB;
use Log;
use App\Libraries\Rights;

/**
 * Bāzes abstraktā klase priekš procesiem, kurus izpilda ar laravel darbiem (jobs)
 */
abstract class Report
{

    /**
     * Parameter if user has access
     * @var boolean 
     */
    private $has_access = false;
    public $report_name = '';
    protected $table_name;

    abstract public function getChartData($group_id, $date_from, $date_to);

    public function __construct($report_name)
    {
        $this->report_name =$report_name;
        $this->validateAccess();
    }

    /**
     * Gets employee's time off view
     */
    public function getView()
    {
        return view('blocks.reports.report', [
                    'uid' => uniqid(),
                    'report_name' => $this->report_name
                ])->render();
    }

    protected function getOverallData($res)
    {
        $gain = 0;
        $loss = 0;
        foreach ($res as $row) {
            $gain += $row->gain;
            $loss += $row->loss;
        }

        $total = $res[count($res) - 1]->total;

        return ['gain' => $gain, 'loss' => $loss, 'total' => $total];
    }

    /**
     * Get rights and check if user has access to employees time off data
     */
    private function getAccess()
    {
        $list = \App\Libraries\DBHelper::getListByTable($this->table_name);

        // Check if register exist for users time off data
        if (!$list) {
            return;
        }

        $list_rights = Rights::getRightsOnList($list->id);

        // Check if user has edit rights on list
        if ($list_rights && $list_rights->is_edit_rights && $list_rights->is_edit_rights == 1) {
            $this->has_access = true;
        }
    }

    /**
     * Validate if user has access to view data. If not then request is aborted.
     */
    private function validateAccess()
    {
        $this->getAccess();

        if (!$this->has_access) {
            abort(403, trans('errors.no_rights_on_register'));
        }
    }
}

<?php

namespace App\Libraries\Blocks\Reports;

use DB;
use Log;
use App\Libraries\Rights;
use App\Exceptions;
use Config;

/**
 * Base class for reporting widgets
 */
abstract class Report
{
    /**
     * Parameter if user has access
     * @var boolean 
     */
    private $has_access = false;

    /**
     * Name which indentifies report object
     * @var string 
     */
    public $report_name = '';

    /**
     * Array which contains columns used in report
     * @var type 
     */
    protected $report_columns = array();

    /**
     * DB table name which is used in reports
     * @var string 
     */
    protected $table_name;

    /**
     * Gets employee's time off data for chart
     * @param integer $group_id Time off types id
     * @param integer $date_from Date from in seconds
     * @param integer $date_to Date to in seconds
     * @return Response JSON response with chart data
     */
    abstract public function getChartData($group_id, $date_from, $date_to);
    
    /**
     * Prepare overall data
     * @param array $res Results
     * @return array Prepared overall results
     */
    abstract public function getOverallData($res);

    /**
     * Class consturctor.
     * @param string $report_name Report's names
     */
    public function __construct($report_name)
    {
        $this->report_name = $report_name;
        $this->validateAccess();
    }

    /**
     * Checks if DB configuration contains option PDO::ATTR_EMULATE_PREPARES = true
     * @throws Exceptions\DXCustomException Throws error if configuration ir wrong
     */
    private function validateConfigurationSettings()
    {
        try {
            $conf = config('database.connections')[config('database.default')]['options'];

            if (!$conf || !$conf[\PDO::ATTR_EMULATE_PREPARES]) {
                throw new Exceptions\DXCustomException("Reporting widget requires that database configuration must contain 'options'=>[PDO::ATTR_EMULATE_PREPARES => true]");
            }
        } catch (\Exception $e) {
            throw new Exceptions\DXCustomException("Reporting widget requires that database configuration must contain 'options'=>[PDO::ATTR_EMULATE_PREPARES => true]");
        }
    }

    /**
     * Gets employee's time off view
     */
    public function getView()
    {
        $this->validateConfigurationSettings();
        
        return view('blocks.reports.report', [
                    'uid' => uniqid(),
                    'report_name' => $this->report_name,
                    'report_columns' => $this->report_columns
                ])->render();
    }

    /**
     * Get rights and check if user has access to employees register (and if so - then have rights to all reports)
     */
    private function getAccess()
    {
        $list_id = Config::get('dx.employee_list_id', 0);

        if ($list_id == 0) {
            return;
        }

        $list_rights = Rights::getRightsOnList($list_id);

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
            //abort(403, trans('errors.no_rights_on_register'));
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_register'));
        }
    }
}
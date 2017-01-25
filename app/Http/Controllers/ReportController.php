<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use App\Libraries\Rights;
use Yajra\Datatables\Datatables;
use App\Libraries\Timeoff\Timeoff;
use DB;
use Config;

/**
 * Employee's time off controller
 */
class ReportController extends Controller
{
    /**
     * Gets employee's time off data for chart
     * @param integer $user_id Employee's user ID
     * @param integer $timeoff_type_id Time off types id
     * @param integer $date_from Date from in seconds
     * @param integer $date_to Date to in seconds
     * @return Response JSON response with chart data
     */
    public function getChartData($report_name, $group_id, $date_from, $date_to)
    {
         $report = \App\Libraries\Blocks\Reports\ReportFactory::initializeReport($report_name);
         
         return $report->getChartData($group_id, $date_from, $date_to);
    }
}

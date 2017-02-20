<?php

namespace App\Libraries\Blocks\EmployeeCount;

use DB;
use Log;
use App\Libraries\Rights;
use App\Exceptions;
use Config;

/**
 * Base class for reporting widgets
 */
abstract class EmployeeCount
{
    /**
     * Name which indentifies report object
     * @var string 
     */
    public $widget_name = '';

    /**
     * Gets employee's time off data for chart
     * @param integer $group_id Time off types id
     * @param integer $date_from Date from in seconds
     * @param integer $date_to Date to in seconds
     * @return Response JSON response with chart data
     */
    abstract public function getGroups();

    /**
     * Prepare overall data
     * @param DateTime $date Date by which data is filtered
     * @return array Prepared overall results
     */
    abstract public function getCounts($date);

    /**
     * Class consturctor.
     * @param string $widget_name Widgets's names
     */
    public function __construct($widget_name)
    {
        $this->widget_name = $widget_name;
    }

    /**
     * Gets employee's time off view
     */
    public function getView($date)
    {
        $count_data = $this->getCounts($date);

        $groups = $this->getGroups();

        return view('blocks.widget_employee_count', [
                    'groups' => $groups,
                    'totalCount' => $count_data['total_count'],
                    'counts' => $count_data['counts'],
                    'widgetName' => $this->widget_name
                ])->render();
    }

    public function getViewUpdate($date)
    {
        $count_data = $this->getCounts($date);

        $groups = $this->getGroups(); //$this->getSources()

        $view = view('blocks.widget_employee_count_body', [
            'groups' => $groups,
            'counts' => $count_data['counts']
                ])->render();

        return ['success' => 1, 'view' => $view, 'total_count' => $count_data['total_count']];
    }

    /**
     * Get total number of employees.
     * @return mixed
     */
    public function getTotalCount($counts)
    {
        $total_count = 0;

        foreach ($counts as $count) {
            $total_count += $count->count;
        }

        return $total_count;
    }
}
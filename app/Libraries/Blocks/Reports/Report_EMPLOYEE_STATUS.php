<?php

namespace App\Libraries\Blocks\Reports;

use DB;
use Config;

/**
 * Report widget for employee status
 */
class Report_EMPLOYEE_STATUS extends Report
{
    /**
     * DB table name which is used in reports
     * @var string 
     */
    protected $table_name = 'dx_users';

    /**
     * Class consturctor. Initialization for chart columns
     * @param string $report_name Report's name
     */
    public function __construct($report_name)
    {
        $this->report_columns = [
            'gain' => [
                'color' => '#26c281',
                'title' => trans('reports.' . $report_name . '.gain'),
                'is_bar' => true
            ],
            'loss' => [
                'color' => '#e7505a',
                'title' => trans('reports.' . $report_name . '.loss'),
                'is_bar' => true
            ],
            'total' => [
                'color' => '#3598dc',
                'title' => trans('reports.' . $report_name . '.total'),
                'is_bar' => false
            ],
        ];

        parent::__construct($report_name);
    }

    /**
     * Prepare overall data
     * @param array $res Results
     * @return array Prepared overall results
     */
    public function getOverallData($res)
    {
        $result = array();

        foreach ($this->report_columns as $key => $col) {
            $result[$key] = 0;
        }

        // Navigate to last position
        end($this->report_columns);
        $last_key = key($this->report_columns);

        foreach ($res as $row) {
            // Skips last column, because it is total column which is not summed
            foreach ($this->report_columns as $key => $col) {
                if ($last_key === $key) {
                    break;
                }

                $result[$key] += $row->$key;
            }
        }

        // Last result's row total value 
        $result[$last_key] = end($res)->$last_key;

        return $result;
    }

    /**
     * Gets employee's time off data for chart
     * @param integer $group_id Time off types id
     * @param integer $date_from Date from in seconds
     * @param integer $date_to Date to in seconds
     * @return Response JSON response with chart data
     */
    public function getChartData($group_id, $date_from, $date_to)
    {
        $date_from_o = new \DateTime();
        $date_from_o->setTimestamp($date_from);

        $date_to_o = new \DateTime();
        $date_to_o->setTimestamp($date_to);

        $args = ['fromDate' => $date_from_o, 'toDate' => $date_to_o];

        $sql_where = '';
        $group_query_join = '';
        if ($group_id > 0) {
            $args['groupId'] = $group_id;

            $sql_where = ' d.source_id = :groupId AND ';
            $group_query_join = ' LEFT JOIN in_departments d ON d.id = u.department_id ';
        }

        $sql_where .= " u.id NOT IN ( '" . implode(Config::get('dx.empl_ignore_ids', array()), "', '") . "' ) AND ";

        $res = DB::select("SELECT 
            cl.year, 
            cl.month, 
            (SELECT COUNT(*) FROM dx_users u " . $group_query_join . " WHERE " . $sql_where . " YEAR(ifnull(u.join_date,'1970-01-01')) = cl.year AND MONTH(ifnull(u.join_date,'1970-01-01')) = cl.month AND ifnull(u.join_date,'1970-01-01') >= :fromDate AND ifnull(u.join_date,'1970-01-01') <= :toDate) as gain,
            (SELECT COUNT(*) FROM dx_users u " . $group_query_join . " WHERE " . $sql_where . " YEAR(u.termination_date) = cl.year AND MONTH(u.termination_date) = cl.month AND u.termination_date >= :fromDate AND u.termination_date <= :toDate) as loss,
            (SELECT COUNT(*) FROM dx_users u " . $group_query_join . "
                    WHERE " . $sql_where . "
                    (
                            ifnull(u.join_date,'1970-01-01') <=  :toDate
                            AND (YEAR(ifnull(u.join_date,'1970-01-01')) < cl.year 
                                    OR (YEAR(ifnull(u.join_date,'1970-01-01')) = cl.year 
                                            AND (MONTH(ifnull(u.join_date,'1970-01-01')) <= cl.month 
                                            )
                                    )
                            )
                            AND (u.termination_date IS NULL 
                                    OR YEAR(u.termination_date) > cl.year 
                                    OR (YEAR(u.termination_date) = cl.year 
                                            AND (MONTH(u.termination_date) > cl.month 
                                                    OR (MONTH(u.termination_date) = cl.month 
                                                            AND (MONTH(u.termination_date) = MONTH(:toDate) AND u.termination_date > :toDate)
                                                    )
                                            )
                                    )
                            )
                    )
            ) as total
            FROM dx_date_classifiers cl
            WHERE cl.year >= YEAR(:fromDate)
            AND cl.year <= YEAR(:toDate)
            AND cl.month >= MONTH(:fromDate)
            AND cl.month <= MONTH(:toDate);", $args);

        $resOverall = $this->getOverallData($res);

        return response()->json(['success' => 1, 'res' => $res, 'total' => $resOverall, 'is_hours' => 1]);
    }
}
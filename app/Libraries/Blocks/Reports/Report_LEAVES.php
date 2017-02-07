<?php

namespace App\Libraries\Blocks\Reports;

use DB;

/**
 * Report widget for employee leaves
 */
class Report_LEAVES extends Report
{
    /**
     * DB table name which is used in reports
     * @var string 
     */
    protected $table_name = 'dx_timeoff_calc';

    /**
     * Class consturctor. Initialization for chart columns
     * @param string $report_name Report's name
     */
    public function __construct($report_name)
    {
        $timeoff_types = \App\Models\Employee\TimeoffType::all();

        foreach ($timeoff_types as $timeoff_type) {
            $this->report_columns[$timeoff_type->id] = [
                'color' => $timeoff_type->color,
                'title' => $timeoff_type->title,
                'is_bar' => true
            ];
        }

        $this->report_columns['total'] = [
            'color' => '#3598dc',
            'title' => trans('reports.' . $report_name . '.total'),
            'is_bar' => false
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

        $total = 0;

        foreach ($res as $row) {
            // Skips last column, because it is total column which is not summed
            foreach ($this->report_columns as $key => $col) {
                if ($last_key === $key) {
                    break;
                }

                $result[$key] += $row->$key;
                $total += $row->$key;
            }
        }

        // Last result's row total value
        $result[$last_key] = $total;

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

        $query = DB::table('dx_date_classifiers AS cl')
                ->select('cl.year', 'cl.month')
                ->leftJoin('dx_timeoff_calc AS tc', function($join) {
                    $join->on('tc.calc_date_month', '=', 'cl.month');
                    $join->on('tc.calc_date_year', '=', 'cl.year');
                    $join->on('tc.amount', '<', DB::Raw(0));
                })
                ->whereRaw('cl.year >= YEAR(?)')
                ->whereRaw('cl.year <= YEAR(?)')
                ->whereRaw('cl.month >= MONTH(?)')
                ->whereRaw('cl.month <= MONTH(?)')
                ->groupBy('cl.year', 'cl.month');

        $timeoff_types = \App\Models\Employee\TimeoffType::all();

        // Bindings array for query initialization
        $bindings = [];

        // Add select for each time off type, if specified source_id (as group_id) then apply filter
        foreach ($timeoff_types as $timeoff_type) {
            if ($group_id > 0) {
                $query->addSelect(DB::Raw('SUM(CASE WHEN d.source_id = ? AND tc.timeoff_type_id = ' . $timeoff_type->id . " then (tc.amount * -1) ELSE 0 END) AS '" . $timeoff_type->id . "'"));
                $bindings[] = $group_id;
            } else {
                $query->addSelect(DB::Raw('SUM(CASE WHEN tc.timeoff_type_id = ' . $timeoff_type->id . " then (tc.amount * -1) ELSE 0 END) AS '" . $timeoff_type->id . "'"));
            }
        }

        // If source is specified (group_id) then apply source filter
        if ($group_id > 0) {
            $query->addSelect(DB::Raw('SUM(CASE WHEN d.source_id = ? then (tc.amount * -1) ELSE 0 END) AS total', ['source_id' => $group_id]))
                    ->leftJoin('dx_users AS u', 'u.id', '=', 'tc.user_id')
                    ->leftJoin('in_departments AS d', 'd.id', '=', 'u.department_id');

            $bindings[] = $group_id;
        } else {
            $query->addSelect(DB::Raw('IFNULL(SUM(tc.amount * -1), 0) AS total'));
        }

        // WHERE parameters are binded last
         $bindings[] = $date_from_o;
         $bindings[] = $date_to_o;
         $bindings[] = $date_from_o;
         $bindings[] = $date_to_o;
         
         $query->setBindings($bindings);
        

        $res = $query->get();

        $resOverall = $this->getOverallData($res);

        return response()->json(['success' => 1, 'res' => $res, 'total' => $resOverall, 'is_hours' => 0]);
    }
}
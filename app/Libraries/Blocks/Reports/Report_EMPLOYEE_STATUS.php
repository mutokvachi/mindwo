<?php

namespace App\Libraries\Blocks\Reports;

use DB;

class Report_EMPLOYEE_STATUS extends Report
{

    protected $table_name = 'dx_users';

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
        
        $group_query = '';
        if($group_id > 0){
            $args['groupId'] = $group_id;
            
            $group_query = ' u.department_id = :groupId AND ';
        }

        $res = DB::select("SELECT 
            cl.year, 
            cl.month, 
            (SELECT COUNT(*) FROM dx_users u WHERE " . $group_query ." YEAR(ifnull(u.join_date,'1970-01-01')) = cl.year AND MONTH(ifnull(u.join_date,'1970-01-01')) = cl.month AND ifnull(u.join_date,'1970-01-01') >= :fromDate AND ifnull(u.join_date,'1970-01-01') <= :toDate) as gain,
            (SELECT COUNT(*) FROM dx_users u WHERE " . $group_query ." YEAR(u.termination_date) = cl.year AND MONTH(u.termination_date) = cl.month AND u.termination_date >= :fromDate AND u.termination_date <= :toDate) as loss,
            (SELECT COUNT(*) FROM dx_users u 
                    WHERE " . $group_query . "
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

        return response()->json(['success' => 1, 'res' => $res, 'total' => $resOverall]);
    }
}

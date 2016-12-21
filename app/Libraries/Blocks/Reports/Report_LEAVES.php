<?php

namespace App\Libraries\Blocks\Reports;

use DB;

class Report_LEAVES extends Report
{

    protected $table_name = 'dx_timeoff_calc';

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

        $res = DB::select('SELECT 
            cl.year, 
            cl.month, 
            (SELECT COUNT(*) FROM dx_users uj WHERE YEAR(uj.valid_from) = cl.year AND MONTH(uj.valid_from) = cl.month AND uj.valid_from >= :fromDate AND uj.valid_from <= :toDate) as gain,
            (SELECT COUNT(*) FROM dx_users ul WHERE YEAR(ul.valid_to) = cl.year AND MONTH(ul.valid_to) = cl.month AND ul.valid_to >= :fromDate AND ul.valid_to <= :toDate) as loss,
            (SELECT COUNT(*) FROM dx_users ua 
                    WHERE 
                    (
                            ua.valid_from <=  :toDate
                            AND (YEAR(ua.valid_from) < cl.year 
                                    OR (YEAR(ua.valid_from) = cl.year 
                                            AND (MONTH(ua.valid_from) <= cl.month 
                                            )
                                    )
                            )
                            AND (ua.valid_to IS NULL 
                                    OR YEAR(ua.valid_to) > cl.year 
                                    OR (YEAR(ua.valid_to) = cl.year 
                                            AND (MONTH(ua.valid_to) > cl.month 
                                                    OR (MONTH(ua.valid_to) = cl.month 
                                                            AND (MONTH(ua.valid_to) = MONTH(:toDate) AND ua.valid_to > :toDate)
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
            AND cl.month <= MONTH(:toDate);', ['fromDate' => $date_from_o, 'toDate' => $date_to_o]);

        $resOverall = $this->getOverallData($res);

        return response()->json(['success' => 1, 'res' => $res, 'total' => $resOverall]);
    }
}

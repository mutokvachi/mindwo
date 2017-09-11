<?php

namespace App\Libraries\Blocks;

use Input;
use Config;
use DB;


class Block_GROUPS_CALENDAR extends Block
{

    /**
     * Render widget and return its HTML.
     *
     * @return string
     */
    public function getHTML()
    {
        /*
            SELECT
                nod.lesson_date
            FROM
                edu_subjects pas
            JOIN edu_subjects_groups gr ON gr.subject_id = pas.id
            JOIN edu_subjects_groups_days nod ON nod.group_id = gr.id
            JOIN edu_modules modul ON modul.id = pas.module_id
            JOIN edu_programms pr ON pr.id = modul.programm_id
            WHERE
                1=1
                AND pas.is_published = 1
                AND gr.is_published = 1
                AND modul.is_published = 1
                AND pr.is_published = 1
                AND nod.lesson_date BETWEEN '2017-07-20' AND '2017-09-05'
            GROUP BY
                nod.lesson_date
            ORDER BY
                nod.lesson_date ASC
         */

        $current = getdate();

        $month = $current['mon'];
        $year = $current['year'];

        $calendar = $this->build_calendar($month, $year);

        $result = view('blocks.groups_calendar.index', [
            'calendar' => $calendar
            //'date_format' => Config::get('dx.txt_date_format', 'd.m.Y'),
        ])->render();

        return $result;
    }

    /**
     * Izgūst bloka JavaScript
     *
     * @return string Bloka JavaScript loģika
     */
    public function getJS()
    {
        return view('blocks.groups_calendar.scripts')->render();
    }

    /**
     * Izgūst bloka CSS
     *
     * @return string Bloka CSS
     */
    public function getCSS()
    {
        return view('blocks.groups_calendar.css')->render();
    }

    /**
     * Izgūst bloka JSON datus
     *
     * @return string Bloka JSON dati
     */
    public function getJSONData()
    {
        return "";
    }

    /**
     * Izgūst bloka parametra vērtības
     * Parametrus norāda lapas HTML teksta veidā speciālos simbolos [[OBJ=...|CODE=...]]
     *
     * @return void
     */
    protected function parseParams()
    {
    }

    public static function build_calendar($month, $year)
    {

        // Get first day of month timestamp
        $firstDay = mktime(0, 0, 0, $month, 1, $year);

        // Create date of first and last day in month from firstDay timestamp
        $firstDayDate = date("Y-m-d", $firstDay);
        $lastDayDate = date("Y-m-t", $firstDay);

        // Get date exactly one week before month and 1 after
        $beforeWeek_ts = strtotime($firstDayDate . ' -1 week');
        $weekBefore = date("Y-m-d", $beforeWeek_ts);

        $afterWeek_ts = strtotime($lastDayDate . ' +1 week');
        $weekAfter = date("Y-m-d", $afterWeek_ts);

        $dateArray = DB::table('edu_subjects AS pas')
            ->select('nod.lesson_date')
            ->join('edu_subjects_groups AS gr', 'gr.subject_id', '=', 'pas.id')
            ->join('edu_subjects_groups_days AS nod', 'nod.group_id', '=', 'gr.id')
            ->join('edu_modules AS modul', 'modul.id', '=', 'pas.module_id')
            ->join('edu_programms AS pr', 'pr.id', '=', 'modul.programm_id')
            ->where('pas.is_published', '=', 1)
            ->where('gr.is_published', '=', 1)
            ->where('modul.is_published', '=', 1)
            ->where('pr.is_published', '=', 1)
            ->where('nod.lesson_date', 'BETWEEN', DB::raw("'" . $weekBefore . "' AND '" . $weekAfter . "'"))
            ->groupBy('nod.lesson_date')
            ->orderBy('nod.lesson_date', 'ASC')
            ->pluck('lesson_date');


        // Create array containing abbreviations of days of week.
        $daysOfWeek = array('P', 'O', 'T', 'C', 'Pk', 'S', 'Sv');

        // How many days does this month contain?
        $numberDays = date('t', $firstDay);

        // Retrieve some information about the first day of the month
        $dateComponents = getdate($firstDay);

        // Get name of the month
        $monthName = trans('calendar.month_arr.'.($dateComponents['mon']-1));

        // What is the index value (0-6) of the first day of the month
        $dayOfWeek = $dateComponents['wday'];

        // -1 Because in Latvia first day of week is Monday not Sunday
        if ($dayOfWeek > 0) {
            $dayOfWeek = $dayOfWeek - 1;
        } else {
            $dayOfWeek = 6;
        }

        $route_next = route('groups_calendar_load', [$firstDayDate, 'next']);
        $route_prev = route('groups_calendar_load', [$firstDayDate, 'prev']);

        // Create calendar header
        $header = "<table data-route-next='$route_next' data-route-prev='$route_prev' id='fpcT' style='width: 100%'>";
        $header .= "<tr><td style='width:40px'><a href='javascript:;' onclick='group_calendar.go(\"prev\");'><img src='http://vas.gov.lv/lv/gx3/gl.gif' alt='«'></a></td>";
        $header .= "<td>$monthName $year</td>";
        $header .= "<td style='width:40px'><a href='javascript:;' onclick='group_calendar.go(\"next\");'><img src='http://vas.gov.lv/lv/gx3/gr.gif' alt='»'></a></td></tr>";
        $header .= "</tbody></table>";

        // Create the table tag opener and day headers
        $calendar = "<table id='fpcal' style='width: 100%'>";
        $calendar .= "<tr>";

        // Create the calendar headers
        foreach ($daysOfWeek as $day) {
            $calendar .= "<td class='fpcD'>$day</td>";
        }

        // Create the rest of the calendar

        // Initiate the day counter, starting with the 1st.

        $currentDay = 1;

        $calendar .= "</tr><tr>";

        // The variable $dayOfWeek is used to
        // ensure that the calendar
        // display consists of exactly 7 columns.

        if ($dayOfWeek > 0) {

            $prevMonthLastDay_ts = strtotime($firstDayDate . ' -1 month');
            $prevMonthLastDay = date('t', $prevMonthLastDay_ts);
            $prevMonth = date('m', $prevMonthLastDay_ts);
            $prevYear = date('Y', $prevMonthLastDay_ts);

            for ($i = $dayOfWeek - 1; $i >= 0; $i--) {
                $day = ($prevMonthLastDay - $i);
                $date = date('Y-m-d', mktime(0, 0, 0, $prevMonth, $day, $prevYear));

                $found = '';
                if (in_array($date, $dateArray)) {
                    $found = '<img src="http://vas.gov.lv/lv/gx3/fpca.gif" alt="">';
                }

                $calendar .= "<td class='fpcE'><div class='inn'>$found</div>" . $day . "</td>";
            }

        }

        $month = str_pad($month, 2, "0", STR_PAD_LEFT);

        $nowMonth = date('n');
        $nowDay = date('j');
        $nowYear = date('Y');

        while ($currentDay <= $numberDays) {

            // Seventh column (Saturday) reached. Start a new row.

            if ($dayOfWeek == 7) {

                $dayOfWeek = 0;
                $calendar .= "</tr><tr>";

            }

            $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
            $date = "$year-$month-$currentDayRel";

            $class = 'fpcN';
            $found = '';
            if (in_array($date, $dateArray)) {
                $class = 'fpcF';
                $found = '<img src="http://vas.gov.lv/lv/gx3/fpca.gif" alt="">';
            }

            if ($nowDay == $currentDay && $nowMonth == $month && $nowYear == $year) {
                $class .= ' fpcC';
            }


            $calendar .= "<td class='$class' rel='$date'><div class='inn'>$found</div>$currentDay</td>";

            // Increment counters

            $currentDay++;
            $dayOfWeek++;

        }


        // Complete the row of the last week in month, if necessary

        if ($dayOfWeek != 7) {


            $remainingDays = 7 - $dayOfWeek;

            for ($n = 1; $n <= $remainingDays; $n++) {
                $nextMonthDays[] = $n;
            }

            foreach ($nextMonthDays as $nextDay) {

                $nextDay_ts = strtotime($date . ' +' . $nextDay . ' days');
                $nextDate = date('Y-m-d', $nextDay_ts);

                $found = '';
                if (in_array($nextDate, $dateArray)) {
                    $found = '<img src="http://vas.gov.lv/lv/gx3/fpca.gif" alt="">';
                }

                $calendar .= "<td class='fpcE'><div class='inn'>$found</div>$nextDay</td>";
            }


        }

        $calendar .= "</tr>";

        $calendar .= "</table>";

        $table = $header . $calendar;

        return $table;

    }
}
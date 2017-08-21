<?php

namespace App\Libraries\Blocks;

use Input;
use Config;
use DB;


class Block_GROUPS_SCHEDULE extends Block
{

    /**
     * Timer name
     * @var string
     */
    public $timer;

    /**
     * Logical variable show timer or no
     * @var bool
     */
    public $show_timer = false;

    /**
     * Render widget and return its HTML.
     *
     * @return string
     */
    public function getHTML()
    {
        /*
          SELECT
            nod.id,
            pas.title,
            mon.short_title,
            DAY(nod.lesson_date) AS diena,
            TIME_FORMAT(nod.time_from, '%H:%i') AS time_from,
            nod.time_to,
            CURRENT_DATE() AS cur_date,
            modul.title,
            pr.title
            FROM
            edu_subjects pas
            JOIN edu_subjects_groups gr ON gr.subject_id = pas.id
            JOIN edu_subjects_groups_days nod ON nod.group_id = gr.id
            JOIN edu_modules modul ON modul.id = pas.module_id
            JOIN edu_programms pr ON pr.id = modul.programm_id
            JOIN dx_months mon ON mon.nr = MONTH(nod.lesson_date)
            WHERE
            1=1
            AND pas.is_published = 1
            AND gr.is_published = 1
            AND modul.is_published = 1
            AND pr.is_published = 1
            AND nod.lesson_date >= CURRENT_DATE()
            ORDER BY
            nod.lesson_date DESC
         */

        $data = DB::table('edu_subjects AS pas')
            ->select(
                'pas.title',
                'nod.id',
                'nod.lesson_date',
                'm.short_title',
                DB::raw("TIME_FORMAT(nod.time_from, '%H:%i') AS time_from"),
                DB::raw("TIME_FORMAT(nod.time_to, '%H:%i') AS time_to"),
                DB::raw('DAY(nod.lesson_date) AS day')
            )
            ->join('edu_subjects_groups AS gr', 'gr.subject_id', '=', 'pas.id')
            ->join('edu_subjects_groups_days AS nod', 'nod.group_id', '=', 'gr.id')
            ->join('edu_modules AS modul', 'modul.id', '=', 'pas.module_id')
            ->join('edu_programms AS pr', 'pr.id', '=', 'modul.programm_id')
            ->join('dx_months AS m', 'm.nr', '=', DB::raw('MONTH(nod.lesson_date)'))
            ->where('pas.is_published', '=', 1)
            ->where('gr.is_published', '=', 1)
            ->where('modul.is_published', '=', 1)
            ->where('pr.is_published', '=', 1)
            ->where('nod.lesson_date', '>=', DB::raw('CURRENT_DATE()'))
            ->orderBy('nod.lesson_date', 'ASC')
            ->get();

        $prev_lesson_date = 0;
        $schedule = [];
        $count = 0;
        $dateArray = [];
        foreach ($data as $d) {

            if ($prev_lesson_date != $d->lesson_date) {

                if ($count >= 8) {
                    break;
                }

                $schedule[$d->lesson_date]['day'] = $d->day;
                $schedule[$d->lesson_date]['month'] = $d->short_title;
                array_push($dateArray, $d->day);
            }

            $schedule[$d->lesson_date]['groups'][$d->id]['title'] = $d->title;
            $schedule[$d->lesson_date]['groups'][$d->id]['time_from'] = $d->time_from;
            $schedule[$d->lesson_date]['groups'][$d->id]['time_to'] = $d->time_to;

            $prev_lesson_date = $d->lesson_date;
            $count++;
        }

        //dd($schedule);
        //dd($dateArray);
/*
        $dateComponents = getdate();

        $month = $dateComponents['mon'];
        $year = $dateComponents['year'];

        $calendar = $this->build_calendar($month, $year, $dateArray);
*/
        $result = view('blocks.groups_schedule.index', [
            'schedule' => $schedule,
            //'calendar' => $calendar
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
        // Return script if need to show timer
        if ($this->show_timer) {
            return view('blocks.groups_schedule.scripts')->render();
        } else {
            return '';
        }
    }

    /**
     * Izgūst bloka CSS
     *
     * @return string Bloka CSS
     */
    public function getCSS()
    {
        // Return style if need to show timer
        if ($this->show_timer) {
            return view('blocks.groups_schedule.css')->render();
        } else {
            return '';
        }
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
        $dat_arr = explode('|', $this->params);

        foreach ($dat_arr as $item) {
            $val_arr = explode('=', $item);

            if ($val_arr[0] == "CODE") {
                $this->timer = str_replace("_", " ", getBlockParamVal($val_arr));
            } else if (strlen($val_arr[0]) > 0) {
                throw new PagesException("Norādīts blokam neatbilstošs parametra nosaukums (" . $val_arr[0] . ")!");
            }
        }
    }

    public function build_calendar($month, $year, $dateArray)
    {

        // Create array containing abbreviations of days of week.
        $daysOfWeek = array('P', 'O', 'T', 'C', 'Pk', 'S', 'Sv');

        // What is the first day of the month in question?
        $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);

        // How many days does this month contain?
        $numberDays = date('t', $firstDayOfMonth);

        // Retrieve some information about the first day of the
        // month in question.
        $dateComponents = getdate($firstDayOfMonth);

        // What is the name of the month in question?
        $monthName = $dateComponents['month'];

        // What is the index value (0-6) of the first day of the
        // month in question.
        $dayOfWeek = $dateComponents['wday'];

        // -1 Because in Latvia first day of week is Monday not Sunday
        if ($dayOfWeek > 0) {
            $dayOfWeek = $dayOfWeek - 1;
        } else {
            $dayOfWeek = 6;
        }

        // Create the table tag opener and day headers
        $calendar = "<table id='fpcal' style='width: 100%'>";
        $calendar .= "<caption>$monthName $year</caption>";
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
            $calendar .= "<td colspan='$dayOfWeek'>&nbsp;</td>";
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
            if (in_array($currentDay, $dateArray)) {
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
            $calendar .= "<td colspan='$remainingDays'>&nbsp;</td>";

        }

        $calendar .= "</tr>";

        $calendar .= "</table>";

        return $calendar;

    }
}
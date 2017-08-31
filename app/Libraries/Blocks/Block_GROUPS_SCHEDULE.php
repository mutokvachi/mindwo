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

        $result = view('blocks.groups_schedule.index', [
            'schedule' => $schedule,
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
        return "";
    }

    /**
     * Izgūst bloka CSS
     *
     * @return string Bloka CSS
     */
    public function getCSS()
    {
        return "";
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
}
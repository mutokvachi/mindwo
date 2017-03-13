<?php

namespace App\Libraries\Blocks;

use Input;
use Config;
use DB;


class Block_TIMER extends Block
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

        $timer = DB::table('dx_timer')->where('sys_name', $this->timer)->first();

        // Return nothing if timer not found
        if (empty($timer)) {
            return '';
        }

        // Collect timer data
        $now = date("Y-m-d H:i:s");
        $show_from = $timer->show_from;
        $show_to = $timer->show_to;

        // Parse datetime into a Unix timestamp for better comparing
        $time_now = strtotime($now);
        $time_from = strtotime($show_from);
        $time_to = strtotime($show_to);

        // Check if it's time to show our timer. Else return nothing
        if ($time_now >= $time_from && $time_now <= $time_to) {
            $this->show_timer = true;
        } else {
            return '';
        }

        $deadline = $timer->deadline;
        $waiting_text = $timer->waiting_text; //'Līdz AS "Sadales tīkls" 10. dzimšanas dienai';
        $success_text = $timer->success_text;

        $result = view('blocks.timer.index', [
            'deadline' => $deadline,
            'waiting_text' => $waiting_text,
            'success_text' => $success_text,
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
            return view('blocks.timer.scripts')->render();
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
            return view('blocks.timer.css')->render();
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
}
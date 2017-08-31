<?php

namespace App\Http\Controllers\Widgets;

use App\Libraries\Blocks\Block_GROUPS_CALENDAR;
use App\Http\Controllers\Controller;

class EventsController extends Controller
{

    public function loadGroupsCalendar($date, $direction)
    {

        if ($direction == "next") {
            $timestamp = strtotime($date . ' +1 month');
        } elseif ($direction == "prev") {
            $timestamp = strtotime($date . ' -1 month');
        } else {
            $timestamp = strtotime($date);
        }

        $month = date('m', $timestamp);
        $year = date('Y', $timestamp);

        $calendar = Block_GROUPS_CALENDAR::build_calendar($month, $year);

        return $calendar;

    }
}

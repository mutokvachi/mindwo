<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use DB;

/**
 * Controlls calendar widget. Returns events for calendar.
 */
class CalendarController extends Controller
{

    /**
     * Gets all evenets in specified data range
     * @param Request $request Requests data containing filter parameters
     * @return array Events which are filtered
     */
    public function getCalendarEvents(Request $request)
    {
        $this->validate($request, [
            'start' => 'required|date',
            'end' => 'required|date'
        ]);

        // Retrieve user
        $fromDate = $request->input('start');

        $toDate = $request->input('end');

        $sourceId = $request->input('source_id');

        $showHolidays = $request->input('show_holidays');
        $showBirthdays = $request->input('show_birthdays');

        $events = [];

        $events['enterprise'] = $this->getEnterpriseEvents($fromDate, $toDate, $sourceId);

        // By default show or if set to 1
        if ($showHolidays == 1) {
            $events['holidays'] = $this->getHolidays($fromDate, $toDate);
        } else {
            $events['holidays'] = [];
        }

        // By default show or if set to 1
        if ($showBirthdays == 1) {
            $events['birthdays'] = $this->getBirthdays($fromDate, $toDate);
        } else {
            $events['birthdays'] = [];
        }

        $result = ['success' => 1, 'data' => $events];

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get all enterprise events
     * @param string $fromDate Filter starting date
     * @param string $toDate Filter ending date
     * @param int $source_id Source ID which is used
     * @return array Array with filtered events
     */
    private function getEnterpriseEvents($fromDate, $toDate, $source_id)
    {
        $events = DB::table('in_events AS e')
                ->leftJoin('in_sources AS s', 's.id', '=', 'e.source_id')
                ->select(DB::raw('e.id, e.title, e.event_time_from AS start, e.event_time_to AS end, s.feed_color AS COLOR'))
                ->where('e.is_active', 1)
                ->where('e.event_time_from', '>=', $fromDate)
                ->whereRaw('((e.event_time_to IS NULL AND e.event_time_from <= ?) OR (e.event_time_to IS NOT NULL AND e.event_time_to <= ?))', [$toDate, $toDate])
                ->where(function ($query) use ($source_id) {
                    if (!$source_id || $source_id == 0) {
                        \Log::info('null');
                        $query->whereNull('e.source_id');
                        $query->orWhere('e.source_id', 0);
                    } else {
                        \Log::info('not null');
                        $query->where('e.source_id', $source_id);
                    }
                })
                ->get();

        return $events;
    }

    /**
     * Gets holidays from system and return them in required format
     * @param string $fromDateRaw Filter starting date
     * @param string $toDateRaw Filter ending date
     * @return array Array with filtered events
     */
    private function getBirthdays($fromDateRaw, $toDateRaw)
    {
        $fromDate = new \DateTime($fromDateRaw);
        $toDate = new \DateTime($toDateRaw);

        $res = DB::table('dx_users AS u')
                ->leftJoin('in_sources AS s', 's.id', '=', 'u.source_id')
                ->select(DB::raw("u.id, u.display_name, u.position_title, s.title AS source_title, u.birth_date"))
                ->get();


        $res = $this->getBirthdaysInRange($res, $fromDate, $toDate);

        $events = [];

        foreach ($res as $event) {
            if (!array_key_exists($event->birth_date, $events)) {
                $events[$event->birth_date] = array();
            }

            $details = [];

            if ($event->position_title) {
                $details[] = $event->position_title;
            }
            if ($event->source_title) {
                $details[] = $event->source_title;
            }

            $name = $event->display_name . (count($details) > 0 ? ' (' . implode(', ', $details) . ')' : '');

            $events[$event->birth_date][] = ['id' => $event->id, 'name' => $name];
        }

        return $events;
    }

    /**
     * Filters birthdays in specified date range
     * @param object $res Results with user data from data base
     * @param string $start Filter starting date in string format
     * @param string $end Filter ending date in string format
     * @return array Filtered results
     */
    private function getBirthdaysInRange($res, $start, $end)
    {
        $new_res = array();

        foreach ($res as $event) {
            if (!$event->birth_date) {
                continue;
            }

            $birthday = new \DateTime($event->birth_date);

            $diffYears = ($start->format("Y") - $birthday->format("Y"));

            if ($diffYears > 0) {
                $temp = $birthday->add(new \DateInterval('P' . $diffYears . 'Y'));
            } else {
                $temp = $birthday->sub(new \DateInterval('P' . ($diffYears * -1) . 'Y'));
            }

            if ($temp < $start) {
                $temp->add(new \DateInterval('P1Y'));
            }

            if ($birthday <= $end && $temp >= $start && $temp <= $end) {
                $event->birth_date = $temp->format("Y-m-d");

                $new_res[] = $event;
            }
        }

        return $new_res;
    }

    /**
     * Gets holidays from system and return them in required format
     * @param string $fromDateRaw Filter starting date
     * @param string $toDateRaw Filter ending date
     * @return array Array with filtered events
     */
    private function getHolidays($fromDateRaw, $toDateRaw)
    {
        $fromDate = date_create($fromDateRaw);
        $toDate = date_create($toDateRaw);

        $fromYear = date("Y", strtotime($fromDateRaw));

        $holidays = DB::table('dx_holidays AS h')
                ->select('c.title AS country', 'h.title AS holiday_title', 'h.is_several_days', 'h.from_year', 'h.to_year', 'mf.nr AS from_month', 'df.code AS from_day', 'mt.nr AS to_month', 'dt.code AS to_day')
                ->leftJoin('dx_months AS mf', 'mf.id', '=', 'h.from_month_id')
                ->leftJoin('dx_month_days AS df', 'df.id', '=', 'h.from_day_id')
                ->leftJoin('dx_months AS mt', 'mt.id', '=', 'h.to_month_id')
                ->leftJoin('dx_month_days AS dt', 'dt.id', '=', 'h.to_day_id')
                ->leftJoin('dx_countries AS c', 'h.country_id', '=', 'c.id')
                ->get();

        $events = [];

        foreach ($holidays as $holiday) {
            $holidayFromDate = $this->prepareHolidayDate($holiday->from_year, $holiday->from_month, $holiday->from_day, $fromYear);

            if ($holiday->is_several_days == 1) {
                $holidayToDate = $this->prepareHolidayDate($holiday->to_year, $holiday->to_month, $holiday->to_day, $fromYear);

                if ($fromDate <= $holidayToDate && $toDate >= $holidayFromDate) {
                    // Need to add 1 day because next date interval function doesn't include last day of period
                    $holidayToDate = $holidayToDate->add(new \DateInterval('P1D'));

                    // We split each multiple day event for each day as seperate event because later thay all are combined into one event un client side
                    $interval = \DateInterval::createFromDateString('1 day');
                    $period = new \DatePeriod($holidayFromDate, $interval, $holidayToDate);

                    foreach ($period as $dt) {
                        $this->groupHolidayEvent($events, $dt, $holiday);
                    }
                }
            } else {
                if ($fromDate <= $holidayFromDate && $toDate >= $holidayFromDate) {
                    $this->groupHolidayEvent($events, $holidayFromDate, $holiday);
                }
            }
        }

        return $events;
    }

    /**
     * Groups holiday events so they can be properly returned
     * @param array $events Array of grouped events
     * @param DateTime $eventDate Formated event date
     * @param object $holiday Holidays data from database
     */
    private function groupHolidayEvent(&$events, $eventDate, $holiday)
    {
        // Groups all data into multidimensional array - events[date][title][country]        
        $eventDateFormated = $eventDate->format("Y-m-d");

        // Group by date
        if (!array_key_exists($eventDateFormated, $events)) {
            $events[$eventDateFormated] = array();
        }

        // Group by holiday title
        if (!array_key_exists($holiday->holiday_title, $events[$eventDateFormated])) {
            $events[$eventDateFormated][$holiday->holiday_title] = array();
        }

        // Group by holiday country
        if ($holiday->country) {
            if (!in_array($holiday->country, $events[$eventDateFormated][$holiday->holiday_title])) {
                $events[$eventDateFormated][$holiday->holiday_title][] = $holiday->country;
            }
        } else {
            $inter_trans = trans('calendar.international');

            if (!in_array($inter_trans, $events[$eventDateFormated][$holiday->holiday_title])) {
                $events[$eventDateFormated][$holiday->holiday_title][] = $inter_trans;
            }
        }
    }

    /**
     * Get date from holday day, month and year values 
     * @param int $year Holiday's year if empty set as current year
     * @param int $month Holiday's month is numeric
     * @param string $day_code Holiday's day, could also be value "LAST" which indicates that it is last day of the month
     * @param int $filter_from_year Current filter's year
     * @return DateTime Prepared date
     */
    private function prepareHolidayDate($year, $month, $day_code, $filter_from_year)
    {
        if (!$year) {
            $year = $filter_from_year;
        }

        if ($day_code != 'LAST') {
            $date_string = $year . '-' . $month . '-' . $day_code;

            return date_create($date_string);
        } else {
            $date_string = $year . '-' . $month . '-01';

            return date("Y-m-t", strtotime($date_string));
        }
    }
}

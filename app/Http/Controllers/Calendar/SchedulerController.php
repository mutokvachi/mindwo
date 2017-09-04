<?php

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\Controller;
use App\Libraries\Rights;
use DB;
use App\Exceptions;
use Illuminate\Http\Request;
use App\Libraries\DBHistory;
use Auth;
use stdClass;
use App\Libraries\DB_DX;

/**
 * Scheduler UI controller
 */
class SchedulerController extends Controller
{
   
    /**
     * List ID for subjects table edu_subjects
     * 
     * @var integer
     */
    private $subjects_list_id = 0;
    
    /**
     * List ID for groups table edu_subjects_groups
     * 
     * @var integer
     */
    private $groups_list_id = 0;
    
    /**
     * List ID for days table edu_subjects_groups_days
     * 
     * @var integer
     */
    private $days_list_id = 0;
    
    /**
     * List ID for rooms table edu_rooms
     * 
     * @var integer
     */
    private $rooms_list_id = 0;
    
    /**
     * List ID for coffee pauses table edu_subjects_groups_days_pauses
     * 
     * @var integer
     */
    private $coffee_list_id = 0;
    
    /**
     * Id for newly created group
     * 
     * @var integer 
     */
    private $new_group_id = 0;
    
    /**
     * Id for newly created group day event
     * 
     * @var integer 
     */
    private $new_day_id = 0;
    
     /**
     * Id for newly created coffee pause
     * 
     * @var integer 
     */
    private $new_cofee_id = 0;
    
    /**
     * Get scheduler page UI
     * 
     * @param integer $current_room_id Room ID or 0 for all rooms
     * @param \Illuminate\Http\Request $request GET request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getSchedulerPage($current_room_id, Request $request)
    {
        $this->checkRights();
        
        $rooms = $this->getRooms();
        
        $current_date = $request->input('current_date', null);
        
        return view('calendar.scheduler.page', [
            'subjects_list_id' => $this->subjects_list_id,
            'groups_list_id' => $this->groups_list_id,
            'days_list_id' => $this->days_list_id,
            'rooms_list_id' => $this->rooms_list_id,
            'coffee_list_id' => $this->coffee_list_id,
            'subjects' => $this->getSubjects(),
            'groups' => $this->getGroups(),
            'rooms' => $rooms,
            'rooms_cbo' => $this->getCboRooms($rooms),
            'current_room_id' => $current_room_id,
            'current_date' => $current_date,
            'page_title' => trans('calendar.scheduler.page_title')
        ]);
    }

    /**
     * Get default scheduler page UI
     * 
     * @param \Illuminate\Http\Request $request GET request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDefaultSchedulerPage(Request $request)
    {
        return $this->getSchedulerPage(0, $request);
    }
    
    /**
     * Return all data in JSON arrays used for scheduler
     * This is used to refresh scheduler UI if related data is updated via AJAX
     * 
     * @param integer $current_room_id Room ID
     * @return \Illuminate\Http\JsonResponse Returns all data arrays in JSON
     */
    public function getSchedulerJSON($current_room_id) {
        $this->checkRights();
        
        $rooms = $this->getRooms();
        
        return response()->json([
            'success' => 1, 
            'subjects' => json_encode($this->getSubjects()),
            'groups' => json_encode($this->getGroups()),
            'rooms' => json_encode($rooms),
            'rooms_cbo' => json_encode($this->getCboRooms($rooms))
        ]);
    }
    
     /**
     * Return events data in JSON arrays used for scheduler
     * This is used to refresh scheduler UI if related data is updated via AJAX
     * 
     * @param integer $current_room_id Room ID
     * @param \Illuminate\Http\Request $request GET request
     * @return \Illuminate\Http\JsonResponse Returns events data arrays in JSON
     */
    public function getSchedulerEventsJSON($current_room_id, Request $request) {
        $this->checkRights();
        
        return json_encode($this->getEvents($current_room_id, $request->input('start'), $request->input('end')));
    }
    
     /**
     * Return rooms data in JSON arrays used for scheduler
     * This is used to refresh scheduler UI if related data is updated via AJAX
     * 
     * @param integer $current_room_id Room ID
     * @param \Illuminate\Http\Request $request GET request
     * @return \Illuminate\Http\JsonResponse Returns rooms data arrays in JSON
     */
    public function getSchedulerRoomsJSON($current_room_id, Request $request) {
        
        if ($current_room_id) {
            return [];
        }
        $this->checkRights();
        
        $rooms = $this->getRooms();
        
        return json_encode($rooms);
    }
    
    /**
     * Creates new group for given subject, room and start/end times
     * 
     * @param \Illuminate\Http\Request $request POST request
     * @return \Illuminate\Http\JsonResponse Returns created items IDs as JSON
     */
    public function createNewGroup(Request $request) {
        $this->validate($request, [
            'subject_id' => 'required|integer|exists:edu_subjects,id',
            'start_time' => 'required',
            'end_time' => 'required',
            'room_id' => 'required|integer|exists:edu_rooms,id',
        ]);
        
        $this->checkRights();
        $room = DB::table('edu_rooms')->where('id', '=', $request->input("room_id"))->first();
        
        // we take default teacher in case if it is only one attached to subject
        $teachers = DB::table('edu_subjects_teachers')
                    ->where('subject_id', '=', $request->input('subject_id'))
                    ->get();
        
        $teacher_id = (count($teachers) == 1) ? $teachers[0]->teacher_id : null;
        $time_from = check_time($request->input("start_time"), "yyyy-mm-dd HH:ii");
        $time_to = check_time($request->input("end_time"), "yyyy-mm-dd HH:ii");
        
        $dx_db_g = (new DB_DX())->table('edu_subjects_groups');
        $dx_db_d = (new DB_DX())->table('edu_subjects_groups_days');
        $dx_db_t = ($teacher_id) ? (new DB_DX())->table('edu_subjects_groups_days_teachers') : null;

        DB::transaction(function () use ($request, $room, $teacher_id, $time_from, $time_to, $dx_db_g, $dx_db_d, $dx_db_t)
        {            
            $this->new_group_id = $dx_db_g->insertGetId([
                'subject_id' => $request->input('subject_id'),
                'seats_limit' => $room->room_limit,
                'main_teacher_id' => $teacher_id
            ]);

            $this->new_day_id = $dx_db_d->insertGetId([
                'group_id' =>  $this->new_group_id,
                'lesson_date' => check_date($request->input("start_time"), "yyyy-mm-dd"),
                'time_from' => $time_from,
                'time_to' => $time_to,
                'room_id' => $room->id
            ]);
            
            if ($dx_db_t) {
                $dx_db_t->insertGetId([
                    'teacher_id' => $teacher_id,
                    'group_day_id' => $this->new_day_id,
                    'time_from' => $time_from,
                    'time_to' => $time_to
                ]);
            }
        });
        
        return response()->json([
            'success' => 1, 
            'group_id' => $this->new_group_id,
            'day_id' => $this->new_day_id
        ]);
    }
    
    /**
     * Update existing day start/end times and/or changes allocated room
     * 
     * @param \Illuminate\Http\Request $request POST request
     * @return \Illuminate\Http\JsonResponse Returns success status
     */
    public function updateDay(Request $request) {
        $this->validate($request, [
            'day_id' => 'required|integer|exists:edu_subjects_groups_days,id',
            'start_time' => 'required',
            'end_time' => 'required',
            'room_id' => 'required|integer|exists:edu_rooms,id',
        ]);
        
        $this->checkRights();
        
         // we check if there is only 1 teacher assigned to group
        $teachers = DB::table('edu_subjects_groups_days_teachers')
                    ->where('group_day_id', '=', $request->input('day_id'))
                    ->get();
        
        $teacher_id = (count($teachers) == 1) ? $teachers[0]->id : null;
        $time_from = check_time($request->input("start_time"), "yyyy-mm-dd HH:ii");
        $time_to = check_time($request->input("end_time"), "yyyy-mm-dd HH:ii");
        
        $group_id = DB::table('edu_subjects_groups_days')
                    ->where('id', '=', $request->input("day_id"))
                    ->first()
                    ->group_id;
        
        $group = DB::table('edu_subjects_groups')
                 ->where('id', '=', $group_id)
                 ->first();
        
        $dx_db_d = (new DB_DX())->table('edu_subjects_groups_days')
                                ->where('id', '=', $request->input("day_id"))
                                ->update([                        
                                    'lesson_date' => check_date($request->input("start_time"), "yyyy-mm-dd"),
                                    'time_from' => $time_from,
                                    'time_to' => $time_to,
                                    'room_id' => $request->input("room_id")
        ]); 

        $dx_db_t = ($teacher_id) ? (new DB_DX())->table('edu_subjects_groups_days_teachers')
                                    ->where('id', '=', $teacher_id)
                                    ->update([
                                        'time_from' => $time_from,
                                        'time_to' => $time_to
                                    ]) : null;
                                    
        $dx_db_g =  ($group->is_published) ? (new DB_DX())->table('edu_subjects_groups')
                                    ->where('id', '=', $group_id)
                                    ->update(['is_published' => 0]) : null;

        DB::transaction(function () use ($dx_db_d, $dx_db_t, $dx_db_g){
            $dx_db_d->commitUpdate();
            
            if ($dx_db_t) {
                $dx_db_t->commitUpdate();
            }
            
            if ($dx_db_g) {
                $dx_db_g->commitUpdate();
            }
        });
        
        return response()->json([
            'success' => 1
        ]);
    }
    
    /**
     * Creates new day for given group, room and start/end times
     * 
     * @param \Illuminate\Http\Request $request POST request
     * @return \Illuminate\Http\JsonResponse Returns created day ID in JSON
     */
    public function newDay(Request $request) {
        $this->validate($request, [
            'group_id' => 'required|integer|exists:edu_subjects_groups,id',
            'start_time' => 'required',
            'end_time' => 'required',
            'room_id' => 'required|integer|exists:edu_rooms,id',
        ]);
        
        $this->checkRights();
        
        $dx_db = (new DB_DX())->table('edu_subjects_groups_days');

        DB::transaction(function () use ($request, $dx_db)
        {
            $this->new_day_id = $dx_db->insertGetId([
                'group_id' => $request->input("group_id"),
                'lesson_date' => check_date($request->input("start_time"), "yyyy-mm-dd"),
                'time_from' => check_time($request->input("start_time"), "yyyy-mm-dd HH:ii"),
                'time_to' => check_date($request->input("end_time"), "yyyy-mm-dd HH:ii"),
                'room_id' => $request->input("room_id")
            ]);
                  
        });
        
        return response()->json([
            'success' => 1,
            'day_id' => $this->new_day_id
        ]);
    }
    
    /**
     * Creates new coffee pause record for given room and start/end times
     * 
     * @param \Illuminate\Http\Request $request POST request
     * @return \Illuminate\Http\JsonResponse Returns created coffee pause ID and associated subject, group and day IDs in JSON
     */
    public function newCoffee(Request $request) {
        $this->validate($request, [            
            'start_time' => 'required',
            'end_time' => 'required',
            'room_id' => 'required|integer|exists:edu_rooms,id',
        ]);
        
        $this->checkRights();
        $room = DB::table('edu_rooms')->where('id', '=', $request->input("room_id"))->first();
        
        $time_from = check_time($request->input("start_time"), "yyyy-mm-dd HH:ii");
        $time_to = check_time($request->input("end_time"), "yyyy-mm-dd HH:ii");
        
        $day = DB::table('edu_subjects_groups_days as d')
                ->select('d.id as day_id', 'd.group_id', 'g.subject_id')
                ->join('edu_subjects_groups as g', 'd.group_id', '=', 'g.id')
                ->where('d.room_id', '=', $room->id)
                ->where('d.lesson_date', '=', check_date($request->input("start_time"), "yyyy-mm-dd"))
                ->where('d.time_from', '<=', $time_from)
                ->where('d.time_to', '>=', $time_to)
                ->first();
        
        if (!$day) {
            throw new Exceptions\DXCustomException(trans('calendar.scheduler.error_no_day_for_coffee'));
        }        
        
        $feed_org_id = $this->getDefaultFeedOrg();
        
        $dx_db = (new DB_DX())->table('edu_subjects_groups_days_pauses');
        DB::transaction(function () use ($time_from, $time_to, $day, $room, $feed_org_id, $dx_db)
        {
            $this->new_cofee_id = $dx_db->insertGetId([
                'room_id' => $room->id,
                'group_day_id' => $day->day_id,
                'time_from' => $time_from,
                'time_to' => $time_to,
                'feed_org_id' => $feed_org_id
            ]);
        });
        
        return response()->json([
            'success' => 1, 
            'group_id' => $day->group_id,
            'day_id' => $day->day_id,
            'coffee_id' => $this->new_cofee_id,
            'subject_id' => $day->subject_id
        ]);
    }
    
    /**
     * Updates existing coffee pause record
     * 
     * @param \Illuminate\Http\Request $request POST request
     * @return \Illuminate\Http\JsonResponse Returns coffee pause ID and associated subject, group and day IDs in JSON
     */
    public function updateCoffee(Request $request) {
        $this->validate($request, [  
            'coffee_id' => 'required|integer|exists:edu_subjects_groups_days_pauses,id',
            'start_time' => 'required',
            'end_time' => 'required',
            'room_id' => 'required|integer|exists:edu_rooms,id',
        ]);
        
        $this->checkRights();
        $room = DB::table('edu_rooms')->where('id', '=', $request->input("room_id"))->first();
        
        $time_from = check_time($request->input("start_time"), "yyyy-mm-dd HH:ii");
        $time_to = check_time($request->input("end_time"), "yyyy-mm-dd HH:ii");
        
        $day = DB::table('edu_subjects_groups_days as d')
                ->select('d.id as day_id', 'd.group_id', 'g.subject_id')
                ->join('edu_subjects_groups as g', 'd.group_id', '=', 'g.id')
                ->where('d.room_id', '=', $room->id)
                ->where('d.lesson_date', '=', check_date($request->input("start_time"), "yyyy-mm-dd"))
                ->where('d.time_from', '<=', $time_from)
                ->where('d.time_to', '>=', $time_to)
                ->first();
        
        if (!$day) {
            throw new Exceptions\DXCustomException(trans('calendar.scheduler.error_no_day_for_coffee'));
        }        
        
        $this->new_cofee_id = $request->input("coffee_id");
        
        $dx_db = (new DB_DX())->table('edu_subjects_groups_days_pauses')
            ->where('id', '=', $this->new_cofee_id)
            ->update([
            'group_day_id' => $day->day_id,
            'time_from' => $time_from,
            'time_to' => $time_to
        ]);

        DB::transaction(function () use ($dx_db){
            $dx_db->commitUpdate();
        });
        
        return response()->json([
            'success' => 1, 
            'group_id' => $day->group_id,
            'day_id' => $day->day_id,
            'coffee_id' => $this->new_cofee_id,
            'subject_id' => $day->subject_id
        ]);
    }
    
    /**
     * Gets default feeding organization ID
     * @return integer Organization ID
     */
    private function getDefaultFeedOrg() {
        $feed = DB::table('edu_orgs_types')->where('code', '=', 'FEED')->first();
        
        if (!$feed) {
            return null;
        }        
        
        $feed_org = DB::table('edu_orgs')
                    ->where('org_type_id', '=', $feed->id)
                    ->where(function($query) {
                        $query->whereRaw('active_till is null or active_till > date(now())');
                    })
                    ->first();
            
        if (!$feed_org) {
            return null;
        }
        
        return $feed_org->id;
    }
    
    /**
     * Prepares array with subjects
     * 
     * @return array
     */
    private function getSubjects() {
        return DB::table('edu_subjects')
                ->orderBy('title')
                ->get();
    }
    
    /**
     * Prepares array with groups 
     * 
     * @return array
     */
    private function getGroups() {
        return DB::table('edu_subjects_groups')
                ->select(
                        'id',
                        'subject_id',
                        'title',
                        DB::raw("case when is_complecting then 'complect' else case when first_publish is null then 'prepare' else 'correct' end end as status")
                )
                ->where('is_published', '=', 0)
                ->orderBy('id')
                ->get();
    }
    
    /**
     * Prepares events and coffee pauses array - used for calendar JSON feed
     * 
     * @param integer $current_room_id Room ID, 0 for all rooms
     * @param string $start Start date in format yyyy-mm-dd
     * @param string $end End date in format yyyy-mm-dd
     * @return array Events and coffee pauses rows array for given date interval
     */
    private function getEvents($current_room_id, $start, $end) {
        
        $coffee = DB::table('edu_subjects_groups_days_pauses as c')
                    ->select(
                            DB::raw("CONCAT('C', c.id) as id"), 
                            'd.room_id as resourceId', 
                            DB::raw("CONCAT(d.lesson_date, 'T', c.time_from) as start"),
                            DB::raw("CONCAT(d.lesson_date, 'T', c.time_to) as end"),
                            DB::raw("'Kafijas pauze' as title"),
                            'g.subject_id as dx_subj_id',
                            'g.id as dx_group_id',
                            'd.id as dx_day_id',
                            'c.id as dx_coffee_id',
                            DB::raw("'cafe' as className"),
                            DB::raw("'#d6df32' as color"),
                            DB::raw("'' as rendering"),
                            DB::raw('0 as dx_is_published')
                    )
                    ->where(function($query) use ($current_room_id) {
                        if ($current_room_id) {
                            $query->where('d.room_id', '=', $current_room_id);
                        }
                    })
                    ->whereBetween('d.lesson_date', [$start, $end])
                    ->join('edu_subjects_groups_days as d', 'c.group_day_id', '=', 'd.id')
                    ->join('edu_subjects_groups as g', 'd.group_id', '=', 'g.id');
                    
        $rooms = DB::table('edu_rooms_calendars as r')
                    ->select(
                            DB::raw("CONCAT('R', r.id) as id"), 
                            'r.room_id as resourceId', 
                            DB::raw("DATE_FORMAT(r.from_time, '%Y-%m-%dT%H:%i') as start"),
                            DB::raw("DATE_FORMAT(r.to_time, '%Y-%m-%dT%H:%i') as end"),
                            DB::raw("'Telpa nav pieejama' as title"),
                            DB::raw('0 as dx_subj_id'),
                            DB::raw('0 as dx_group_id'),
                            DB::raw('0 as dx_day_id'),
                            DB::raw('0 as dx_coffee_id'),
                            DB::raw("'closed' as className"),
                            DB::raw("'#ff9f89' as color"),
                            DB::raw("'background' as rendering"),
                            DB::raw('0 as dx_is_published')
                    )
                    ->where(function($query) use ($current_room_id) {
                        if ($current_room_id) {
                            $query->where('r.room_id', '=', $current_room_id);
                        }
                    });
                    
        $coffee_rooms = DB::table('edu_subjects_groups_days_pauses as cr')
                    ->select(
                            DB::raw("CONCAT('RC', cr.id) as id"), 
                            'cr.room_id as resourceId', 
                            DB::raw("CONCAT(d.lesson_date, 'T', cr.time_from) as start"),
                            DB::raw("CONCAT(d.lesson_date, 'T', cr.time_to) as end"),
                            DB::raw("'Telpa nav pieejama' as title"),
                            DB::raw('0 as dx_subj_id'),
                            DB::raw('0 as dx_group_id'),
                            DB::raw('0 as dx_day_id'),
                            DB::raw('0 as dx_coffee_id'),
                            DB::raw("'closed' as className"),
                            DB::raw("'#ff9f89' as color"),
                            DB::raw("'background' as rendering"),
                            DB::raw('0 as dx_is_published')
                    )
                    ->join('edu_subjects_groups_days as d', 'cr.group_day_id', '=', 'd.id')
                    ->whereRaw('cr.room_id != d.room_id')
                    ->where(function($query) use ($current_room_id) {
                        if ($current_room_id) {
                            $query->where('cr.room_id', '=', $current_room_id);
                        }
                    });
                    
        $holidays = DB::table('dx_holidays as h')
                    ->select(
                            DB::raw("CONCAT('H', r.id, '-', h.id) as id"), 
                            'r.id as resourceId', 
                            DB::raw("CONCAT(ifnull(h.from_year,year(now())), '-', LPAD(m.nr, 2, '0'), '-', LPAD(d.code, 2, '0'), 'T00:00') as start"),
                            DB::raw("CONCAT(ifnull(h.from_year,year(now())), '-', LPAD(m.nr, 2, '0'), '-', LPAD(d.code, 2, '0'), 'T23:59') as end"),
                            DB::raw("'Brīvdiena' as title"),
                            DB::raw('0 as dx_subj_id'),
                            DB::raw('0 as dx_group_id'),
                            DB::raw('0 as dx_day_id'),
                            DB::raw('0 as dx_coffee_id'),
                            DB::raw("'closed' as className"),
                            DB::raw("'#ff9f89' as color"),
                            DB::raw("'background' as rendering"),
                            DB::raw('0 as dx_is_published')
                    )
                    ->join('dx_months as m', 'h.from_month_id', '=', 'm.id')
                    ->join('dx_month_days as d', 'h.from_day_id', '=', 'd.id')
                    ->join('edu_rooms as r', 'r.id', '>', DB::raw('0'));
        
        return  DB::table('edu_subjects_groups_days as d')
                ->select(
                        'd.id', 
                        'd.room_id as resourceId', 
                        DB::raw("CONCAT(d.lesson_date, 'T', d.time_from) as start"),
                        DB::raw("CONCAT(d.lesson_date, 'T', d.time_to) as end"),
                        'g.title',
                        'g.subject_id as dx_subj_id',
                        'g.id as dx_group_id',
                        'd.id as dx_day_id',
                        DB::raw('0 as dx_coffee_id'),
                        DB::raw("'group' as className"),
                        DB::raw("'#69a4e0' as color"),
                        DB::raw("'' as rendering"),
                        'g.is_published as dx_is_published'
                )
                ->where(function($query) use ($current_room_id) {
                    if ($current_room_id) {
                        $query->where('d.room_id', '=', $current_room_id);
                    }
                })
                ->whereBetween('d.lesson_date', [$start, $end])
                ->join('edu_subjects_groups as g', 'd.group_id', '=', 'g.id')
                ->union($coffee)
                ->union($rooms)
                ->union($holidays)
                ->union($coffee_rooms)
                ->orderBy('start')
                ->get();
    }
    
    /**
     * Prepares rooms array
     * 
     * @return array Rooms
     */
    private function getRooms() {
        
        return DB::table('edu_rooms as r')
                    ->select('r.id', 'o.title as organization', DB::raw("case when r.is_elearn then r.title else CONCAT(r.title, ' - ', ifnull(r.room_address, o.address)) end as title"))
                    ->join('edu_orgs as o', 'r.org_id', '=', 'o.id')
                    ->orderBy('o.title')
                    ->orderBy('r.title')
                    ->get();
    }
    
    /**
     * Appends "All rooms" item to rooms array
     * 
     * @param array $rooms Rooms array
     * @return array Rooms array with apended "All rooms" item (with ID = 0)
     */
    private function getCboRooms($rooms) {
        
        $nt = new stdClass();

        $nt->id = 0;
        $nt->organization = "Visas organizācijas";
        $nt->title = "Visas telpas";
        
        array_push($rooms, $nt);
        return $rooms;
    }
    
    /**
     * Check user rights on list for table edu_subjects_groups
     * 
     * @param type $list_id
     * @throws Exceptions\DXCustomException
     */
    private function checkRights() {
        
        $this->subjects_list_id = \App\Libraries\DBHelper::getListByTable('edu_subjects')->id;
        $this->groups_list_id = \App\Libraries\DBHelper::getListByTable('edu_subjects_groups')->id;
        $this->days_list_id = \App\Libraries\DBHelper::getListByTable('edu_subjects_groups_days')->id;
        $this->rooms_list_id = \App\Libraries\DBHelper::getListByTable('edu_rooms')->id;
        $this->coffee_list_id = \App\Libraries\DBHelper::getListByTable('edu_subjects_groups_days_pauses')->id;
        
        $rights = Rights::getRightsOnList($this->subjects_list_id);

        if ($rights == null) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_register'));
        }
    }
}

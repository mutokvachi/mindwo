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
     * Returns scheduler page
     */
    public function getSchedulerPage($current_room_id)
    {
        $this->checkRights();
        
        $rooms = $this->getRooms();
        
        return view('calendar.scheduler.page', [
            'subjects_list_id' => $this->subjects_list_id,
            'groups_list_id' => $this->groups_list_id,
            'days_list_id' => $this->days_list_id,
            'rooms_list_id' => $this->rooms_list_id,
            'coffee_list_id' => $this->coffee_list_id,
            'subjects' => DB::table('edu_subjects')->orderBy('title')->get(),
            'groups' => DB::table('edu_subjects_groups')->where('is_published', '=', 0)->orderBy('id')->get(),
            'rooms' => $rooms,
            'rooms_cbo' => $this->getCboRooms($rooms),
            'events' => $this->getEvents($current_room_id),
            'current_room_id' => $current_room_id
        ]);
    }
    
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
        $time_to = check_date($request->input("end_time"), "yyyy-mm-dd HH:ii");
        
        DB::transaction(function () use ($request, $room, $teacher_id, $time_from, $time_to)
        {
            $this->new_group_id = DB::table('edu_subjects_groups')->insertGetId([
                'subject_id' => $request->input('subject_id'),
                'seats_limit' => $room->room_limit
            ]);

            $this->new_day_id = DB::table('edu_subjects_groups_days')->insertGetId([
                'group_id' =>  $this->new_group_id,
                'lesson_date' => check_date($request->input("start_time"), "yyyy-mm-dd"),
                'time_from' => $time_from,
                'time_to' => $time_to,
                'room_id' => $room->id
            ]);
            
            if ($teacher_id) {
                DB::table('edu_subjects_groups_days_teachers')->insert([
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
        $time_to = check_date($request->input("end_time"), "yyyy-mm-dd HH:ii");
        
        DB::transaction(function () use ($request, $time_from, $time_to, $teacher_id)
        {
            DB::table('edu_subjects_groups_days')
                    ->where('id', '=', $request->input("day_id"))
                    ->update([                        
                        'lesson_date' => check_date($request->input("start_time"), "yyyy-mm-dd"),
                        'time_from' => $time_from,
                        'time_to' => $time_to,
                        'room_id' => $request->input("room_id")
            ]);
            
           if ($teacher_id) {
                DB::table('edu_subjects_groups_days_teachers')
                        ->where('id', '=', $teacher_id)
                        ->update([
                            'time_from' => $time_from,
                            'time_to' => $time_to
                        ]);
            }
        });
        
        return response()->json([
            'success' => 1
        ]);
    }
    
    public function newDay(Request $request) {
        $this->validate($request, [
            'group_id' => 'required|integer|exists:edu_subjects_groups,id',
            'start_time' => 'required',
            'end_time' => 'required',
            'room_id' => 'required|integer|exists:edu_rooms,id',
        ]);
        
        $this->checkRights();
        
        DB::transaction(function () use ($request)
        {
            $this->new_day_id = DB::table('edu_subjects_groups_days')->insertGetId([
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
    
    public function newCoffee(Request $request) {
        $this->validate($request, [            
            'start_time' => 'required',
            'end_time' => 'required',
            'room_id' => 'required|integer|exists:edu_rooms,id',
        ]);
        
        $this->checkRights();
        $room = DB::table('edu_rooms')->where('id', '=', $request->input("room_id"))->first();
        
        $time_from = check_time($request->input("start_time"), "yyyy-mm-dd HH:ii");
        $time_to = check_date($request->input("end_time"), "yyyy-mm-dd HH:ii");
        
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
        
        DB::transaction(function () use ($time_from, $time_to, $day, $room)
        {
            $this->new_cofee_id = DB::table('edu_subjects_groups_days_pauses')->insertGetId([
                'room_id' => $room->id,
                'group_day_id' => $day->day_id,
                'time_from' => $time_from,
                'time_to' => $time_to
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
        $time_to = check_date($request->input("end_time"), "yyyy-mm-dd HH:ii");
        
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
        
        DB::transaction(function () use ($time_from, $time_to, $day)
        {
            DB::table('edu_subjects_groups_days_pauses')
                    ->where('id', '=', $this->new_cofee_id)
                    ->update([
                    'group_day_id' => $day->day_id,
                    'time_from' => $time_from,
                    'time_to' => $time_to
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
    
    private function getEvents($current_room_id) {
        
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
                            DB::raw("'#d6df32' as color")
                    )
                    ->where(function($query) use ($current_room_id) {
                        if ($current_room_id) {
                            $query->where('d.room_id', '=', $current_room_id);
                        }
                    })
                    ->join('edu_subjects_groups_days as d', 'c.group_day_id', '=', 'd.id')
                    ->join('edu_subjects_groups as g', 'd.group_id', '=', 'g.id');
        
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
                        DB::raw("'#69a4e0' as color")
                )
                ->where(function($query) use ($current_room_id) {
                    if ($current_room_id) {
                        $query->where('d.room_id', '=', $current_room_id);
                    }
                })
                ->join('edu_subjects_groups as g', 'd.group_id', '=', 'g.id')
                ->union($coffee)
                ->orderBy('start')
                ->get();
    }
    
    private function getRooms() {
        
        return DB::table('edu_rooms as r')
                    ->select('r.id', 'o.title as organization', 'r.title as title')
                    ->join('edu_orgs as o', 'r.org_id', '=', 'o.id')
                    ->orderBy('o.title', 'r.title')
                    ->get();
    }
    
    private function getCboRooms($rooms) {
        
        $nt = new stdClass();

        $nt->id = 0;
        $nt->organization = "Visas telpas";
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

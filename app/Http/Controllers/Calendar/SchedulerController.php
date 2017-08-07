<?php

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\Controller;
use App\Libraries\Rights;
use DB;
use App\Exceptions;
use mindwo\pages\Menu;
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
        
        DB::transaction(function () use ($request, $room)
        {
            $this->new_group_id = DB::table('edu_subjects_groups')->insertGetId([
                'subject_id' => $request->input('subject_id'),
                'seats_limit' => $room->room_limit
            ]);

            $this->new_day_id = DB::table('edu_subjects_groups_days')->insertGetId([
                'group_id' =>  $this->new_group_id,
                'lesson_date' => check_date($request->input("start_time"), "yyyy-mm-dd"),
                'time_from' => check_time($request->input("start_time"), "yyyy-mm-dd HH:ii"),
                'time_to' => check_date($request->input("end_time"), "yyyy-mm-dd HH:ii"),
                'room_id' => $room->id
            ]);
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
        
        DB::transaction(function () use ($request)
        {
            DB::table('edu_subjects_groups_days')
                    ->where('id', '=', $request->input("day_id"))
                    ->update([                        
                        'lesson_date' => check_date($request->input("start_time"), "yyyy-mm-dd"),
                        'time_from' => check_time($request->input("start_time"), "yyyy-mm-dd HH:ii"),
                        'time_to' => check_date($request->input("end_time"), "yyyy-mm-dd HH:ii"),
                        'room_id' => $request->input("room_id")
            ]);
        });
        
        return response()->json([
            'success' => 1
        ]);
    }
    
    private function getEvents($current_room_id) {
        return  DB::table('edu_subjects_groups_days as d')
                ->select(
                        'd.id', 
                        'd.room_id as resourceId', 
                        DB::raw("CONCAT(d.lesson_date, 'T', d.time_from) as start"),
                        DB::raw("CONCAT(d.lesson_date, 'T', d.time_to) as end"),
                        'g.title',
                        'g.subject_id as dx_subj_id',
                        'g.id as dx_group_id',
                        'd.id as dx_day_id'
                )
                ->where(function($query) use ($current_room_id) {
                    if ($current_room_id) {
                        $query->where('d.room_id', '=', $current_room_id);
                    }
                })
                ->join('edu_subjects_groups as g', 'd.group_id', '=', 'g.id')
                ->orderBy('d.lesson_date')
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
        
        $rights = Rights::getRightsOnList($this->subjects_list_id);

        if ($rights == null) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_register'));
        }
    }
}

<?php

namespace App\Http\Controllers\Meetings;

use App\Http\Controllers\Controller;
use DB;
use App\Exceptions; 
use Auth;
use Illuminate\Http\Request;

/**
 * Reports groups controller
 */
class MeetingsController extends Controller
{
    const MEETING_ACTIVE = "Active";
    const MEETING_ACTIVE_ID = 2;
    
    const MEETING_FUTURE = "Future";
    const MEETING_FUTURE_ID = 1;
    
    const MEETING_PAST = "Past";
    const MEETING_PAST_ID = 3;
    
    const AGENDA_IN_PROCESS = "IN_PROCESS";
    const AGENDA_IN_PROCESS_ID = 2;
    
    const AGENDA_PROCESSED = "PROCESSED";
    const AGENDA_PROCESSED_ID = 3;
    
    /**
     * Current meeting type ID beeing processed
     * @var integer
     */
    private $meeting_type_id = 0;
        
    /**
     * Get default meeting
     * @return Response
     */
    public function getDefault($meeting_type_id) {
        $this->meeting_type_id = $meeting_type_id;
        
        $meeting_row = $this->getAllMeetings(0)->first();
        
        if (!$meeting_row) {
            return $this->showNoRightsError();
        }
        
        return redirect()->route('meeting', ['meeting_type_id' => $meeting_type_id, 'meeting_id' => $meeting_row->id]);        
    }
    
    /**
     * Get report views for given group
     * 
     * @param integer $group_id Report group ID
     * @return Response
     */
    public function getById($meeting_type_id, $meeting_id) {
        
        $this->meeting_type_id = $meeting_type_id;
                
        $meeting_row = $this->getAllMeetings($meeting_id)->first();
        
        if (!$meeting_row) {
            return $this->showNoRightsError();
        }
        
        return $this->getMeetingView($meeting_row);
    }
    
    public function getAgenda(Request $request) {
        $agenda_id = $request->input('agenda_id', 0);
        
        $agenda = DB::table('dx_meetings_agendas as a')
                    ->join('dx_meetings as m', 'a.meeting_id', '=', 'm.id')
                    ->leftJoin('dx_meetings_agendas_statuses as as', 'a.status_id', '=', 'as.id')
                    ->leftJoin('dx_meetings_statuses as ms', 'm.status_id', '=', 'ms.id')
                    ->leftJoin('dx_meetings_types as mt', 'm.meeting_type_id', '=', 'mt.id')
                    ->select(
                            'm.meeting_time',
                            'ms.code as group_type',
                            'a.*', 
                            'as.title as status',
                            'as.code as status_code',
                            'mt.role_decide_id'
                    )
                    ->where('a.id', '=', $agenda_id)
                    ->first();
        
        $decider_row = DB::table('dx_users_roles')->where('role_id', '=', $agenda->role_decide_id)
                ->where('user_id', '=', Auth::user()->id)->first();
        
        $html = view('meetings.agenda_data', [
                    'this' => $this,
                    'agenda' => $agenda,
                    'is_decider' => ($decider_row) ? 1 : 0
		])->render();
        
        return response()->json(['success' => 1, 'status' => $agenda->status, 'html' => $html]);
    }
    
    public function getAgendasList(Request $request) {
        $meeting_id = $request->input('meeting_id', 0);
        
        $added_agendas = $this->getMeetingAgendas($meeting_id);
        
        $html = view('meetings.agenda_list', [
                    'this' => $this,
                    'added_agendas' => $added_agendas,
                    'meeting_row' => $this->getAllMeetings($meeting_id)
		])->render();
        
        return response()->json(['success' => 1, 'html' => $html]);
    }
    
    public function startMeeting($meeting_type_id, $meeting_id) {
        $this->meeting_type_id = $meeting_type_id;
        
        // close all open/active meetings
        DB::table('dx_meetings')->where('status_id', '=', self::MEETING_ACTIVE_ID)->update(['status_id' => self::MEETING_PAST_ID]);
        
        // set this meeting as active
        DB::table('dx_meetings')->where('id', '=', $meeting_id)->update(['status_id' => self::MEETING_ACTIVE_ID]);
        
        return $this->nextAgenda($meeting_type_id, $meeting_id);
    }
    
    public function endMeeting($meeting_type_id, $meeting_id) {
        $this->meeting_type_id = $meeting_type_id;
        
        // close all open/active agendas for this meeting if any
        DB::table('dx_meetings_agendas')->where('meeting_id', '=', $meeting_id)->where('status_id', '=', self::AGENDA_IN_PROCESS_ID)->update(['status_id' => self::AGENDA_PROCESSED_ID]);
        
        // set this meeting as ended
        DB::table('dx_meetings')->where('id', '=', $meeting_id)->update(['status_id' => self::MEETING_PAST_ID]);
        
        return redirect()->route('meeting', ['meeting_type_id' => $meeting_type_id, 'meeting_id' => $meeting_id]);
    }
    
    public function nextAgenda($meeting_type_id, $meeting_id) {
        $this->meeting_type_id = $meeting_type_id;                
        $curent_index = 0;
        
        // try to get current agenda
        $current =  DB::table('dx_meetings_agendas')->where('meeting_id', '=', $meeting_id)->where('status_id', '=', self::AGENDA_IN_PROCESS_ID)->first();
                
        if ($current) {
            // close current agenda
            DB::table('dx_meetings_agendas')->where('id', '=', $current->id)->update(['status_id' => self::AGENDA_PROCESSED_ID]);
            $curent_index = $current->order_index;
        }
        
        // try to get next agenda
        $next = DB::table('dx_meetings_agendas')->where('meeting_id', '=', $meeting_id)->where('order_index', '>', $curent_index)->orderBy('order_index')->first();
        
        if (!$next) {
            // no more agendas, lets close meeting
            return $this->endMeeting($meeting_type_id, $meeting_id);
        }
        
        DB::table('dx_meetings_agendas')->where('id', '=', $next->id)->update(['status_id' => self::AGENDA_IN_PROCESS_ID]);
        
        return redirect()->route('meeting', ['meeting_type_id' => $meeting_type_id, 'meeting_id' => $meeting_id]);
    }
    
    /**
     * Prepare response for given group - to display all related views
     * 
     * @param object $group_row Group row (from table dx_views_reports_groups)
     * @return Response
     */
    private function getMeetingView($meeting_row) {
        
        if (!$meeting_row) {            
            return $this->showNoRightsError();          
        }
        
        $agendas = $this->getMeetingAgendas($meeting_row->id);        
                 
        $meetings = $this->getAllMeetings(0)->get();
    
        $meeting_type_row = DB::table('dx_meetings_types')->where('id', '=', $this->meeting_type_id)->first();
        
        $moderator_row = DB::table('dx_users_roles')->where('role_id', '=', $meeting_type_row->role_moderator_id)
                ->where('user_id', '=', Auth::user()->id)->first();
        
        $prepare_row = DB::table('dx_users_roles')->where('role_id', '=', $meeting_type_row->role_prepare_id)
                ->where('user_id', '=', Auth::user()->id)->first();
        
        $decider_row = DB::table('dx_users_roles')->where('role_id', '=', $meeting_type_row->role_decide_id)
                ->where('user_id', '=', Auth::user()->id)->first();
        
        return  view('meetings.index', [
                    'this' => $this,
                    'meeting_row' => $meeting_row,
                    'agendas' => $agendas,
                    'meetings_actual' => array_filter($meetings, array( $this, 'filterActual')),
                    'meetings_past' => array_filter($meetings, array( $this, 'filterPast')),
                    'meetings_future' => array_filter($meetings, array( $this, 'filterFuture')),
                    'meeting_type_row' => $meeting_type_row,
                    'is_moderator' => ($moderator_row) ? 1 : 0,
                    'is_preparer' => ($prepare_row) ? 1 : 0,
                    'is_decider' => ($decider_row) ? 1 : 0
		]);
    }
    
    private function getMeetingAgendas($meeting_id) {
        return DB::table('dx_meetings_agendas as a')
                    ->leftJoin('dx_meetings_agendas_statuses as as', 'a.status_id', '=', 'as.id')
                    ->select(
                            'a.*', 
                            'as.title as status',
                            'as.code as status_code'
                    )
                    ->where('a.meeting_id', '=', $meeting_id)
                    ->orderBy('a.order_index')
                    ->get();
    }
    
    /**
     * Detect if meeting array item is Actual
     * @param string $value Value to be tested
     * @return type
     */
    private function filterActual($value) {
        return ($value->group_type == self::MEETING_ACTIVE);
    }
    
    /**
     * Detect if meeting array item is Past
     * @param string $value Value to be tested
     * @return type
     */
    private function filterPast($value) {
        return ($value->group_type == self::MEETING_PAST);
    }
    
    /**
     * Detect if meeting array item is Future
     * @param string $value Value to be tested
     * @return type
     */
    private function filterFuture($value) {
        return ($value->group_type == self::MEETING_FUTURE);
    }
    
    /**
     * Prepare groups query object
     * @return object Laravel db query object
     */
    private function getAllMeetings($meeting_id) {
        return DB::table('dx_meetings as m')                    
                    ->join('dx_meetings_types as t', 'm.meeting_type_id', '=', 't.id')
                    ->leftJoin('dx_meetings_agendas as a', 'm.id', '=', 'a.meeting_id')  
                    ->leftJoin('dx_meetings_statuses as ms', 'ms.id', '=', 'm.status_id')
                    ->select(
                            'm.id',
                            'm.meeting_type_id',
                            'm.meeting_time', 
                            DB::raw('count(*) as total_agenda'),
                            'ms.title as status',
                            'ms.code as group_type'                    )
                    ->where('m.meeting_type_id', '=', $this->meeting_type_id)
                    ->where(function($query) use ($meeting_id) {
                        if ($meeting_id > 0) {
                            $query->where('m.id', '=', $meeting_id);
                        }
                    })
                    ->whereExists(function($query) {
                        $query->select(DB::raw(1))                          
                          ->from('dx_users_roles as ur')
                          ->where(function($q) {
                              $q->whereRaw('t.role_prepare_id = ur.role_id or t.role_moderator_id = ur.role_id or t.role_decide_id = ur.role_id');                                
                          })                          
                          ->whereRaw('ur.user_id = ' . Auth::user()->id);
                    })
                    ->groupBy('m.id')
                    ->orderBy('group_type')
                    ->orderBy('meeting_time', 'DESC');
    }
    
    /**
     * Render error page with no rights message
     * @return Response
     */
    private function showNoRightsError() {
        return  view('errors.attention', [
                    'page_title' => trans('errors.access_denied_title'),
                    'message' => trans('errors.no_rights_on_meetings')
		]);
    }

}

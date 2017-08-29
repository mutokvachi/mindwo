<?php

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\Controller;
use App\Libraries\Rights;
use DB;
use App\Exceptions;
use Illuminate\Http\Request;
use Config;
use App\Libraries\DBHistory;
use Auth;
use stdClass;
use App\Libraries\DB_DX;

/**
 * Complecting UI controller
 */
class ComplectController extends Controller
{    
    
    /**
     * Array with current logged in user organizations - ID's only
     * 
     * @var array
     */
    private $user_orgs_arr = [];
        
    /**
     * Indicates id current logged in user is main coordinator and have full rights
     * 
     * @var boolean 
     */
    private $is_main_coord = false;
    
    /**
     * Get complecting page UI
     * 
     * @param integer $current_org_id Organization ID or 0 for all organizations
     * @param \Illuminate\Http\Request $request GET request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getComplectPage($current_org_id, Request $request)
    {
        $this->checkRights($current_org_id);
                
        $current_date = $request->input('current_date', null);
        $orgs = $this->getOrgsCbo();

        return view('calendar.complect.page', [
            'groups' => $this->getGroups($current_org_id),            
            'current_date' => $current_date,
            'current_org_id' => $current_org_id,
            'orgs' => $orgs,
            'orgs_count' => count($orgs),
            'page_title' => trans('calendar.complect.page_title')
        ]);
    }
    
     /**
     * Return events data in JSON arrays used for complecting calendar
     * This is used to refresh scheduler UI if related data is updated via AJAX
     *      
     * @param integer $current_org_id Organization ID or 0 for all organizations
     * @param \Illuminate\Http\Request $request GET request
     * @return \Illuminate\Http\JsonResponse Returns events data arrays in JSON
     */
    public function getcomplectEventsJSON($current_org_id, Request $request) {
        $this->checkRights($current_org_id);
                
        return json_encode($this->getEvents($current_org_id, $request->input('start'), $request->input('end')));
    }

    /**
     * Return groups data in JSON response
     * This is used to refresh complecting UI
     *      
     * @param integer $current_org_id Organization ID or 0 for all organizations
     * @return \Illuminate\Http\JsonResponse Returns groups HTML in JSON response
     */
    public function getComplectGroupsJSON($current_org_id) {
        $this->checkRights($current_org_id);
        $orgs = $this->getOrgsCbo();
        return response()->json([
            'success' => 1,
            'htm' => view('calendar.complect.group_box', [
                            'groups' => $this->getGroups($current_org_id),
                            'orgs_count' => count($orgs)
                         ])->render()            
        ]);
    }

    /**
     * Return gorup data in JSON format used for AJAX loaded form info UI
     *
     * @param integer $org_id Organization ID
     * @param integer $group_id Group ID
     * @return \Illuminate\Http\JsonResponse Returns group data in JSON format
     */
    public function getGroupInfoJSON($org_id, $group_id) {        

        $this->checkRights($org_id, $group_id);

        $groups = $this->getGroups($org_id, $group_id);

        if (count($groups) == 0) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_group'));
        }

        $orgs = $this->getOrgsCbo();

        $empl_count = $this->getAvailEmpl($org_id, $group_id, true);

        $empl_list = DB::table('dx_lists')
                        ->where('list_title', '=', trans('db_dx_users.list_title_student'))
                        ->first();

        $members = $this->getGroupMembers($org_id, $group_id);

        return response()->json([
            'success' => 1,
            'htm' => view('calendar.complect.group_info', [
                            'group' => $groups[0],
                            'orgs_count' => count($orgs),
                            'days' => $this->getGroupDays($group_id),
                            'avail_empl' => $this->getAvailEmpl($org_id, $group_id),
                            'empl_count' => $empl_count,
                            'is_ajax' => ($empl_count > Config::get('education.empl_load_limit', 300)) ? 1 : 0,
                            'members' => $members,
                            'empl_list_id' => $empl_list->id
                         ])->render()            
        ]);
    }
    
    /**
     * Add employee to group
     *
     * @param \Illuminate\Http\Request $request POST request
     * @return  \Illuminate\Http\JsonResponse Returns success status in JSON format
     */
    public function addGroupMember(Request $request) {
        $this->validate($request, [
            'org_id' => 'required|integer|exists:edu_orgs,id',
            'group_id' => 'required|integer|exists:edu_subjects_groups,id',
            'empl_id' => 'required|integer|exists:dx_users,id'
        ]);

        $org_id = $request->input("org_id");
        $group_id = $request->input("group_id");
        $empl_id = $request->input("empl_id");

        $this->checkRights($org_id, $group_id);
        
        $groups = $this->getGroups($org_id, $group_id);

        if (count($groups) == 0) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_group'));
        }

        $dx_db = (new DB_DX())->table('edu_subjects_groups_members');
        DB::transaction(function () use ($org_id, $group_id, $empl_id, $dx_db)
        {
            $dx_db->insertGetId([
                'org_id' => $org_id,
                'group_id' => $group_id,
                'student_id' => $empl_id
            ]);
        });
        
        return response()->json([
            'success' => 1
        ]);
    }

    /**
     * Removes employee from group
     *
     * @param \Illuminate\Http\Request $request POST request
     * @return  \Illuminate\Http\JsonResponse Returns success status in JSON format
     */
    public function removeGroupMember(Request $request) {
        $this->validate($request, [
            'org_id' => 'required|integer|exists:edu_orgs,id',
            'group_id' => 'required|integer|exists:edu_subjects_groups,id',
            'empl_id' => 'required|integer|exists:dx_users,id'
        ]);

        $org_id = $request->input("org_id");
        $group_id = $request->input("group_id");
        $empl_id = $request->input("empl_id");

        $this->checkRights($org_id, $group_id);
        
        $groups = $this->getGroups($org_id, $group_id);

        if (count($groups) == 0) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_group'));
        }

        $group_member =  DB::table('edu_subjects_groups_members')
                ->where('org_id', '=', $org_id)
                ->where('group_id', '=', $group_id)
                ->where('student_id', '=', $empl_id)
                ->first();
        
        if ($group_member) {
            $dx_db = (new DB_DX())->table('edu_subjects_groups_members')
                    ->where('id', '=', $group_member->id)
                    ->delete();

            DB::transaction(function () use ($dx_db){
                $dx_db->commitDelete();                
            });
        }

        return response()->json([
            'success' => 1
        ]);
    }

    /**
     * Gets organization employees array or total rows count
     *
     * @param integer $org_id Organization ID
     * @param integer $group_id Groupd ID
     * @param boolean $is_count Indicates if results is for counting rows (true) or rows array (false)
     * @return mixed Array with employees | Total rows count
     */
    private function getAvailEmpl($org_id, $group_id, $is_count = false) {
        $rows = DB::table('edu_orgs_users as ou');

        if (!$is_count) {
            $rows->select(
                'u.id',
                'u.display_name',
                'u.person_code',
                'ou.job_title',
                'ou.email',
                'ou.phone',
                'ou.mobile',
                DB::raw('(SELECT count(*) WHERE exists(select 1 from edu_subjects_groups_members m WHERE m.student_id = u.id AND m.group_id = ' . $group_id . ' AND m.org_id = ' . $org_id . ')) as is_member')
            );
        }

        $rows->join('dx_users as u', 'ou.user_id', '=', 'u.id')
             ->where('ou.org_id', '=', $org_id)
             ->whereRaw('(ou.end_date is null or ou.end_date >= date(now()))');

        if ($is_count) {
            return $rows->count();
        }
        else {
            return $rows->take(Config::get('education.empl_load_limit', 300))
                        ->orderBy('u.display_name')
                        ->get();
        }
        
    }

    private function getGroupMembers($org_id, $group_id) {
        return DB::table('edu_subjects_groups_members as m')
                ->select(
                    'u.id',
                    'u.display_name',
                    'u.person_code',
                    'ou.job_title',
                    'ou.email',
                    'ou.phone',
                    'ou.mobile',
                    DB::raw('1 as is_member')
                )
                ->join('edu_orgs_users as ou', 'm.student_id', '=', 'ou.user_id')
                ->join('dx_users as u', 'ou.user_id', '=', 'u.id')
                ->where('m.group_id', '=', $group_id)
                ->where('m.org_id', '=', $org_id)
                ->where('ou.org_id', '=', $org_id)
                ->whereRaw('(ou.end_date is null or ou.end_date >= date(now()))')
                ->orderBy('u.display_name')
                ->get();
    }

    /**
     * Returns array with group days
     *
     * @param integer $group_id
     * @return array
     */
    private function getGroupDays($group_id) {
        return DB::table('edu_subjects_groups_days as d')
               ->select(
                   'd.lesson_date',
                   'd.time_from',
                   'd.time_to',
                   DB::raw('case when r.room_address is null then o.address else r.room_address end as room_address'),
                   'r.room_nr'
               )
               ->join('edu_rooms as r', 'd.room_id', '=', 'r.id')
               ->join('edu_orgs as o', 'r.org_id', '=', 'o.id')
               ->where('d.group_id', '=', $group_id)
               ->orderBy('time_from')
               ->get();
    }

    /**
     * Rerurns user's organizations array for dropdown filling
     *
     * @return array
     */
    private function getOrgsCbo() {
        $orgs = DB::table('edu_orgs as o')
               ->select('o.id', 'o.title')
               ->join('edu_orgs_types as t', 'o.org_type_id', '=', 't.id')
               ->where(function($query) {
                   if (!$this->is_main_coord) {
                       $query->whereIn('o.id', $this->user_orgs_arr);
                   }
               })
               ->where('t.code', '=', 'EDU')
               ->orderBy('o.title')
               ->get();              
               
        if (count($orgs) == 1) {
            return $orgs;
        }
        
        $nt = new stdClass();
        $nt->id = 0;
        $nt->title = "VISAS ORGANIZĀCIJAS";
        
        array_push($orgs, $nt);
        return $orgs;
    }
    
    /**
     * Fills class variable user_orgs_arr with current logged in user's organizations
     */
    private function fillOrgsArray() {        
        
        $orgs = DB::table('edu_orgs_users')
                ->select('org_id')
                ->where('user_id', '=', Auth::user()->id)
                ->whereRaw('end_date is null or end_date > date(now())')
                ->get();

        foreach($orgs as $org) {
            array_push($this->user_orgs_arr, $org->org_id);
        }       
        
    }
    
    /**
     * Prepares events and coffee pauses array - used for calendar JSON feed
     * 
     * @param integer $current_org_id Organization ID or 0 for all organizations
     * @param string $start Start date in format yyyy-mm-dd
     * @param string $end End date in format yyyy-mm-dd
     * @return array Events and coffee pauses rows array for given date interval
     */
    private function getEvents($current_org_id, $start, $end) {
        
        $org_sql = '(SELECT group_concat(org_id) FROM edu_subjects_groups_orgs go WHERE go.group_id=g.id';
        
        if (!$this->is_main_coord) {
            $org_sql .= ' AND go.org_id in (' . implode(",", $this->user_orgs_arr) . ')' ;
        }

        if ($current_org_id) {
            $org_sql .= " AND go.org_id = " .  $current_org_id;
        }
        $org_sql .= ') as orgs';
        \Log::info("ORGS SQL: " . $org_sql . " USER ARR: " . json_encode($this->user_orgs_arr));

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
                        'g.is_published as dx_is_published',
                        DB::raw($org_sql)
                )
                ->join('edu_subjects_groups as g', 'd.group_id', '=', 'g.id')                
                ->whereBetween('d.lesson_date', [$start, $end])                
                ->where('g.is_complecting', '=', true)
                ->where(function($query) use ($current_org_id) {
                    if (!$this->is_main_coord) {                
                        $query->whereExists(function ($query2) use ($current_org_id) {
                            $query2->select(DB::raw(1))
                                  ->from('edu_subjects_groups_orgs as o')
                                  ->whereIn('o.org_id', $this->user_orgs_arr)
                                  ->whereRaw('o.group_id = g.id')
                                  ->where(function($query3) use ($current_org_id) {
                                        if ($current_org_id) {
                                            $query3->where('o.org_id', '=', $current_org_id);
                                        }
                                  });
                        });
                    }
                    else {
                        if ($current_org_id) {
                            $query->whereExists(function ($query2) use ($current_org_id) {
                                $query2->select(DB::raw(1))
                                      ->from('edu_subjects_groups_orgs as o')
                                      ->where('o.org_id', '=', $current_org_id)
                                      ->whereRaw('o.group_id = g.id');
                            });
                        }
                    }
                })
                ->orderBy('start')
                ->get();
    }
    
    /**
     * Prepares array with groups 
     * 
     * @param integer $current_org_id Organization ID or 0 for all organizations
     * @param integer $group_id Group ID or 0 if all groups
     * @return array
     */
    private function getGroups($current_org_id, $group_id = 0) {
        
        $groups = DB::table('edu_subjects_groups as g')
                ->select(
                        'g.id',
                        'g.subject_id',
                        'g.title',
                        'o.title as org_title',
                        'o.id as org_id',
                        'og.places_quota',
                        DB::raw("(SELECT count(m.id) FROM edu_subjects_groups_members as m
                                WHERE m.group_id = g.id AND m.org_id = og.org_id
                                GROUP BY m.group_id) as member_count"),
                        's.title as subject_title',
                        's.subject_code',
                        'm.title as module_title',
                        'm.code as module_code',
                        'p.title as programm_title',
                        'p.code as programm_code',
                        'g.signup_due'                     
                )
                ->join('edu_subjects_groups_orgs as og', 'og.group_id', '=', 'g.id')
                ->join('edu_orgs as o', 'og.org_id', '=', 'o.id')
                ->join('edu_subjects as s', 'g.subject_id', '=', 's.id')
                ->join('edu_modules as m', 's.module_id', '=', 'm.id')
                ->join('edu_programms as p', 'm.programm_id', '=', 'p.id')
                ->where('g.is_complecting', '=', 1)
                ->whereExists(function ($query) use ($current_org_id) {
                    if (!$this->is_main_coord) {  
                        $query->select(DB::raw(1))
                            ->from('edu_subjects_groups_orgs as o')
                            ->whereIn('o.org_id', $this->user_orgs_arr)
                            ->whereRaw('o.group_id = g.id')
                            ->where(function($query3) use ($current_org_id) {
                                  if ($current_org_id) {
                                      $query3->where('o.org_id', '=', $current_org_id);
                                  }
                            });
                    }
                    else {
                        if ($current_org_id) {
                            $query->whereExists(function ($query2) use ($current_org_id) {
                                $query2->select(DB::raw(1))
                                      ->from('edu_subjects_groups_orgs as o')
                                      ->where('o.org_id', '=', $current_org_id)
                                      ->whereRaw('o.group_id = g.id');
                            });
                        }
                    }
                })
                ->where(function($query) use ($current_org_id) {
                    if ($current_org_id) {
                        $query->where('og.org_id', '=', $current_org_id);
                    }
                    else {
                        if (!$this->is_main_coord) {
                            $query->whereIn('og.org_id', $this->user_orgs_arr);
                        }
                    }
                })
                ->where(function($query) use ($group_id) {
                    if ($group_id) {
                        $query->where('g.id', '=', $group_id);
                    }
                })
                ->orderBy('g.id')
                ->get();
                
        foreach($groups as $group) {
            $group->status = ($group->places_quota <= $group->member_count) ? 'full' : 'free';
        }
        
        return $groups;
    }

     /**
      * Check user rights on complecting functionality
      *
      * @param integer $current_org_id Organization ID or 0 for all organizations
      * @throws Exceptions\DXCustomException
      * @return void
      */
    private function checkRights($current_org_id, $gorup_id = 0) {
        
        $main_coord = DB::table('dx_users_roles')
                        ->where('user_id', '=', Auth::user()->id)
                        ->where('role_id', '=', Config::get('education.roles.main_coord', 0))
                        ->first();
        
        $this->is_main_coord = ($main_coord) ? true : false;
        
        if ($this->is_main_coord) {
            return;
        }
        
        $org_coord = DB::table('dx_users_roles')
                        ->where('user_id', '=', Auth::user()->id)
                        ->where('role_id', '=', Config::get('education.roles.org_coord', 0))
                        ->first();

        if (!$org_coord) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_complect'));
        }
        
        $this->fillOrgsArray();
        
        if ($current_org_id && (array_search($current_org_id, $this->user_orgs_arr) === false)) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_organization'));
        }
    }
}

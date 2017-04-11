<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use DB;
use App\Exceptions; 
use Auth;

/**
 * Reports groups controller
 */
class ReportsController extends Controller
{
    /**
     * Get default report group views
     * @return Response
     */
    public function getDefault() {
        
        $group_row = $this->getAllGroups()->first();
        
        if (!$group_row) {            
            return $this->showNoRightsError();          
        }
        
        return $this->getGroupView($group_row);
        
    }
    
    /**
     * Get report views for given group
     * 
     * @param integer $group_id Report group ID
     * @return Response
     */
    public function getByGroup($group_id) {
        $group_row = DB::table('dx_views_reports_groups')
                     ->where('id', '=', $group_id)
                     ->first();
        
        return $this->getGroupView($group_row);
    }
    
    /**
     * Prepare response for given group - to display all related views
     * 
     * @param object $group_row Group row (from table dx_views_reports_groups)
     * @return Response
     */
    private function getGroupView($group_row) {
        
        $views = DB::table('dx_views as v')
                    ->leftJoin('dx_views_log as vl', 'v.id', '=', 'vl.view_id')
                    ->select(
                        'v.id',
                        'v.title',
                        DB::raw('max(vl.view_time) as last_viewed')
                    )
                    ->where('v.group_id', '=', $group_row->id)
                    ->whereExists(function($query) {
                        $query->select(DB::raw(1))
                          ->from('dx_users_roles as ur')
                          ->join('dx_roles_lists as rl', 'ur.role_id', '=', 'rl.role_id')
                          ->whereRaw('ur.user_id = ' . Auth::user()->id)
                          ->whereRaw('rl.list_id = v.list_id');
                    })
                    ->groupBy('v.id')
                    ->orderBy('v.title')
                    ->get();
        
        if (count($views) == 0) {
            return $this->showNoRightsError();
        }            
                    
        $groups = $this->getAllGroups()->get();
        
        return  view('reports.index', [
                    'group_row' => $group_row,
                    'views' => $views,
                    'groups' => $groups
		]);
    }
    
    /**
     * Prepare groups query object
     * @return object Laravel db query object
     */
    private function getAllGroups() {
        return DB::table('dx_views_reports_groups as g')
                    ->join('dx_views as vv', 'g.id', '=', 'vv.group_id')
                    ->select('g.id', 'g.title', 'g.order_index', 'g.icon', DB::raw('count(*) as total_views'))
                    ->whereExists(function($query) {
                        $query->select(DB::raw(1))                          
                          ->from('dx_users_roles as ur')
                          ->join('dx_roles_lists as rl', 'ur.role_id', '=', 'rl.role_id')
                          ->join('dx_views as v', 'rl.list_id', '=', 'v.list_id')
                          ->whereRaw('g.id = v.group_id')
                          ->whereRaw('ur.user_id = ' . Auth::user()->id)
                          ->whereRaw('rl.list_id = v.list_id');
                    })
                    ->groupBy('g.id')
                    ->orderBy('order_index');
    }
    
    /**
     * Render error page with no rights message
     * @return Response
     */
    private function showNoRightsError() {
        return  view('errors.attention', [
                    'page_title' => trans('errors.access_denied_title'),
                    'message' => trans('errors.no_rights_on_reports')
		]);
    }

}

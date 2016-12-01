<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use App\Libraries\Rights;
use Yajra\Datatables\Datatables;
use App\Libraries\Timeoff\Timeoff;
use DB;
use Config;

/**
 * Employee's time off controller
 */
class TimeoffController extends Controller
{

    /**
     * Parameter if user has access
     * @var boolean 
     */
    public $has_access = false;
    
    public $has_my_access = false;

    /**
     * Gets employee's time off view
     * @param integer $user_id Employee's user ID
     */
    public function getView($user_id)
    {
        $user = \App\User::find($user_id);
        
       $this->getAccess($user);
      $this->validateAccess();
        
        

        return view('profile.tab_timeoff', [
                    'user' => $user,
                    'has_access' => ($this->has_access)
                ])->render();
    }

    /**
     * Calculate timeoff data
     * 
     * @param integer $user_id Employee ID for which to calculate
     * @param integer $timeoff_id Timeoff type ID for which to calculate
     * @return Response JSON response with status info
     */
    public function calculateTimeoff($user_id, $timeoff_id)
    {
        
        $timeoff = new Timeoff($user_id, $timeoff_id);
        $timeoff->calculate();

        $timeoff_row = DB::table('dx_timeoff_types as to')
                ->where('to.id', '=', $timeoff_id)
                ->first();

        $balance = DB::table('dx_timeoff_calc')
                ->where('user_id', '=', $user_id)
                ->where('timeoff_type_id', '=', $timeoff_id)
                ->orderBy('calc_date', 'DESC')
                ->first();

        $time = ($balance) ? $balance->balance : 0;
        $unit = trans('calendar.hours');

        if (!$timeoff_row->is_accrual_hours) {
            $time = round(($time / Config::get('dx.working_day_h', 8)));
            $unit = trans('calendar.days');
        }

        return response()->json(['success' => 1, 'balance' => $time, 'unit' => $unit]);
    }

    /**
     * Gets employee's time off calculated table
     * @param integer $user_id Employee's user ID
     * @param integer $timeoff_type_id Time off types id
     * @param integer $year Time off year
     */
    public function getTable($user_id, $timeoff_type_id, $year)
    {
        
        $user = \App\User::find($user_id);

        return Datatables::of($user->timeoffCalc()->with('timeoffRecordType')
                                ->where('timeoff_type_id', $timeoff_type_id)
                                ->where(DB::Raw('YEAR(calc_date)'), $year)
                        )
                        ->make(true);
    }

    /**
     * Get rights and check if user has access to employees time off data
     */
    public function getAccess($user)
    {
        if($user->id == \Auth::user()->id){
             $this->has_my_access = true; 
        }
        
        $list = \App\Libraries\DBHelper::getListByTable('dx_timeoff_calc');

        // Check if register exist for users time off data
        if (!$list) {
            return;
        }

        $list_rights = Rights::getRightsOnList($list->id);

        // Check if user has edit rights on list
        if ($list_rights && $list_rights->is_edit_rights && $list_rights->is_edit_rights == 1) {
            $this->has_access = true;
        } else {
            return;
        }
    }

    /**
     * Validate if user has access to employee time off tab. If not then request is aborted.
     */
    private function validateAccess()
    {
        if (!$this->has_access && !$this->has_my_access) {
            abort(403, trans('errors.no_rights_on_register'));
        }
    }
}

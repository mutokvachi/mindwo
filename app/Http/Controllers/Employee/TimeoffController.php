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
     * Parameter if user has HR access
     * @var boolean 
     */
    public $has_hr_access = false;

    /**
     * Parameter if user is owner of opened profile
     * @var boolean 
     */
    public $has_my_access = false;

    /**
     * Gets employee's time off view
     * @param integer $user_id Employee's user ID
     */
    public function getView($user_id)
    {
        $user = \App\User::find($user_id);

        $this->getAccess($user);

        // User with any access type can view this data
        $this->validateAccess();

        return view('profile.tab_timeoff', [
                    'user' => $user,
                    'has_hr_access' => ($this->has_hr_access)
                ])->render();
    }

    /**
     * Returns HTML for the year filter view
     * @param integer $user_id Employee's user ID
     * @return string View's HTML
     */
    public function getYearFilterView($user_id)
    {
        $user = \App\User::find($user_id);

        $this->getAccess($user);

        // User with any access type can view this data
        $this->validateAccess();

        $filter_all_years = $user->timeoffYears()->get();

        return view('profile.timeoff.control_timeoff_filter_year', [
                    'filter_all_years' => $filter_all_years
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
        $user = \App\User::find($user_id);

        $this->getAccess($user);

        // Only user with HR access can view this data
        $this->validateHrAccess();

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

        return response()->json(['success' => 1, 'balance' => floor($time), 'unit' => $unit]);
    }

    /**
     * Delete timeoff data
     * 
     * @param integer $user_id Employee ID for which to calculate
     * @param integer $timeoff_id Timeoff type ID for which to calculate
     * @return Response JSON response with status info
     */
    public function deleteTimeoff($user_id, $timeoff_id)
    {
        DB::transaction(function () use ($user_id, $timeoff_id) {
            DB::table('dx_timeoff_calc')
                    ->where('user_id', '=', $user_id)
                    ->where('timeoff_type_id', '=', $timeoff_id)
                    ->delete();
        });

        $timeoff_row = DB::table('dx_timeoff_types as to')
                ->where('to.id', '=', $timeoff_id)
                ->first();

        $unit = trans('calendar.hours');

        if (!$timeoff_row->is_accrual_hours) {

            $unit = trans('calendar.days');
        }

        return response()->json(['success' => 1, 'balance' => 0, 'unit' => $unit]);
    }
    
    /**
     * Gets employee's time off total data for specified period
     * @param integer $user_id Employee's user ID
     * @param integer $timeoff_type_id Time off types id
     * @param integer $date_from Date from in seconds
     * @param integer $date_to Date to in seconds
     * @return Object total data values for period
     */
    public function getPeriodData($user_id, $timeoff_type_id, $date_from, $date_to){        
        return DB::table('dx_timeoff_calc AS c')
                ->leftJoin('dx_timeoff_types AS t', 't.id', '=', 'c.timeoff_type_id')
                ->select(DB::Raw('SUM(CASE WHEN c.amount >= 0 then c.amount ELSE 0 END) AS accrued'),
                        DB::Raw('SUM(CASE WHEN c.amount < 0 then c.amount ELSE 0 END) * -1 AS used'),
                        DB::Raw("SUBSTRING_INDEX( GROUP_CONCAT(c.balance ORDER BY c.calc_date DESC), ',', 1 ) AS balance"),
                        DB::Raw('MIN(t.is_accrual_hours) AS is_accrual_hours'))
                ->where('c.user_id', $user_id)
                ->where('c.timeoff_type_id', $timeoff_type_id)
                                ->where('c.calc_date', '>=', $date_from)
                                ->where('c.calc_date', '<=', $date_to)
                ->groupBy('c.user_id')
                ->get();
    }
    
    /**
     * Gets employee's time off data for chart
     * @param integer $user_id Employee's user ID
     * @param integer $timeoff_type_id Time off types id
     * @param integer $date_from Date from in seconds
     * @param integer $date_to Date to in seconds
     * @return Response JSON response with chart data
     */
    public function getChartData($user_id, $timeoff_type_id, $date_from, $date_to) {
        $user = \App\User::find($user_id);

        $this->getAccess($user);

        // User with any access type can view this data
        $this->validateAccess();

        $date_from_o = new \DateTime();
        $date_from_o->setTimestamp($date_from);

        $date_to_o = new \DateTime();
        $date_to_o->setTimestamp($date_to);
        
        $res = DB::table('dx_timeoff_calc')
                ->select(DB::Raw('SUM(CASE WHEN amount >= 0 then amount ELSE 0 END) AS accrued'),
                        DB::Raw('SUM(CASE WHEN amount < 0 then amount ELSE 0 END) * -1 AS used'),
                        DB::Raw("SUBSTRING_INDEX( GROUP_CONCAT(balance ORDER BY calc_date DESC), ',', 1 ) AS balance"),
                        'calc_date_month',
                        'calc_date_year')
                ->where('user_id', $user_id)
                ->where('timeoff_type_id', $timeoff_type_id)
                                ->where('calc_date', '>=', $date_from_o)
                                ->where('calc_date', '<=', $date_to_o)
                ->groupBy('calc_date_year', 'calc_date_month')
                ->get();
        
        $resTotal = $this->getPeriodData($user_id, $timeoff_type_id, $date_from_o, $date_to_o);
        
        return response()->json(['success' => 1, 'res' => $res, 'total' => $resTotal]);                
    }

    /**
     * Gets employee's time off calculated table
     * @param integer $user_id Employee's user ID
     * @param integer $timeoff_type_id Time off types id
     * @param integer $date_from Date from in seconds
     * @param integer $date_to Date to in seconds
     * @return Yajra\Datatables
     */
    public function getTable($user_id, $timeoff_type_id, $date_from, $date_to)
    {
        $user = \App\User::find($user_id);

        $this->getAccess($user);

        // User with any access type can view this data
        $this->validateAccess();

        $date_from_o = new \DateTime();
        $date_from_o->setTimestamp($date_from);

        $date_to_o = new \DateTime();
        $date_to_o->setTimestamp($date_to);

        return Datatables::of($user->timeoffCalc()->with('timeoffRecordType')
                                ->where('timeoff_type_id', $timeoff_type_id)
                                ->where('calc_date', '>=', $date_from_o)
                                ->where('calc_date', '<=', $date_to_o)
                        )
                        ->make(true);
    }

    /**
     * Get rights and check if user has access to employees time off data
     * @param \App\User $user User whoes profile is being viewed
     * @return type
     */
    public function getAccess($user)
    {
        if ($user && $user->id == \Auth::user()->id) {
            $this->has_my_access = true;
        }

        $list = \App\Libraries\DBHelper::getListByTable('dx_timeoff_calc');

        // Check if register exist for users time off data
        if (!$list) {
            return;
        }

        $list_rights = Rights::getRightsOnList($list->id);

        // Check if user has edit rights on list
        if (!($list_rights && $list_rights->is_edit_rights && $list_rights->is_edit_rights == 1)) {
            return;
        }
        
        if(Rights::isSuperviseOnItem($user->dx_supervise_id)) {
            $this->has_hr_access = true;
        }
    }

    /**
     * Validate if user has any access to employee time off tab. If not then request is aborted.
     */
    private function validateAccess()
    {
        if (!$this->has_hr_access && !$this->has_my_access) {
            abort(403, trans('errors.no_rights_on_register'));
        }
    }

    /**
     * Validate if user has HR access to employee time off tab. If not then request is aborted.
     */
    private function validateHrAccess()
    {
        if (!$this->has_hr_access) {
            abort(403, trans('errors.no_rights_on_register'));
        }
    }

    /**
     * Checks user rights to perform calculation deletion
     * 
     * @throws Exceptions\DXCustomException
     */
    private function checkDeleteRights()
    {
        $list = DBHelper::getListByTable('dx_timeoff_calc');
        $right = Rights::getRightsOnList($list->id);

        if ($right == null) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_register'));
        }

        if ($right->is_delete_rights == 0) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_to_delete'));
        }
    }
}

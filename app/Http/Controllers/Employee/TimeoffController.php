<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use App\Libraries\Rights;

/**
 * Employee's time off controller
 */
class TimeoffController extends Controller
{

    /**
     * Parameter if user has manager rights
     * @var boolean 
     */
    public $has_manager_access = false;

    /**
     * Parameter if user has HR (Human resources) rights
     * @var boolean 
     */
    public $has_hr_access = false;

    /**
     * Gets employee's time off view
     * @param integer $user_id Employee's user ID
     */
    public function getView($user_id)
    {
        $user = \App\User::find($user_id);

        //$this->getAccess($user);

        //$this->validateAccess();

        return view('profile.tab_timeoff', [
                    'user' => $user,
                    'has_hr_access' => $this->has_hr_access,
                    'has_manager_access' => $this->has_manager_access
                ])->render();
    }
    
    /**
     * Gets employee's time off calculated table
     * @param integer $user_id Employee's user ID
     * @param integer $timeoff_type_id Time off types id
     */
    public function getCalcTable($user_id, $timeoff_type_id){
        $user = \App\User::find($user_id);
        
        return $user->timeoffCalc()->where('timeoff_type_id', $timeoff_type_id)->get();
    }

    /**
     * Get rights and check if user has access to employees time off data
     * @param \App\User $user Users model
     */
    public function getAccess($user)
    {
        $this->has_hr_access = $this->getHRAccess();

        // List edit rights are checked only for HR users. 
        // If user has manager access then it doesnt matter if he has access to list
        if (!$this->getListEditRights()) {
            $this->has_hr_access = false;
        }

        if (!$this->has_hr_access) {
            $this->has_manager_access = $this->getManagerAccess($user);
        }
    }

    /**
     * Check if user has manager access
     * @param \App\User $user User's model to who manager will be checked
     * @return boolean True if has manager access
     */
    private function getManagerAccess($user)
    {
        if ($user && $user->manager_id == \Auth::user()->id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if user has HR access
     * @return boolean True if has HR access
     */
    private function getHRAccess()
    {
        // Get rights for employee list
        $list_rights = Rights::getRightsOnList(config('dx.employee_list_id'));

        // If user has rights the he has HR rights
        if ($list_rights && $list_rights->is_edit_rights && $list_rights->is_edit_rights == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check if user has edit rights
     * @return boolean True if has rights
     */
    private function getListEditRights()
    {
        $list = \App\Libraries\DBHelper::getListByTable('in_employees_notes');

        // Check if register exist for users time off data
        if (!$list) {
            return false;
        }

        $list_rights = Rights::getRightsOnList($list->id);

        // Check if user has edit rights on list
        if ($list_rights && $list_rights->is_edit_rights && $list_rights->is_edit_rights == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Validate if user has access to employee time off tab. If not then request is aborted.
     */
    private function validateAccess()
    {
        if (!$this->has_hr_access && !$this->has_manager_access) {
            abort(403, trans('errors.no_rights_on_register'));
        }
    }
}

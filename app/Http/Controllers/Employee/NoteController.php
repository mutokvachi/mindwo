<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use App\Libraries\Rights;

/**
 * Employee's notes controller
 */
class NoteController extends Controller
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
     * Constructs employee documents class. Sets needed parameters
     */
    public function __construct()
    {
        
    }

    /**
     * Gets employee's notes view
     * @param integer $user_id Employee's user ID
     */
    public function getView($user_id)
    {
        $user = \App\User::find($user_id);

        $this->getAccess($user);

        $this->validateAccess();

        return view('profile.tab_notes', [
            'user' => $user,
            'has_hr_access' => $this->has_hr_access,
            'has_manager_access' => $this->has_manager_access
        ])->render();
    }

    /**
     * Saves data
     * @param Request $request Data request
     * @return string Result
     */
    public function save(Request $request)
    {        
        // Retrieve user
        $user_id = $request->input('user_id');        
        $user = \App\User::find($user_id);

        // Check if user has edit rights to tab
        $this->getAccess($user);
        $this->validateAccess();
        
        $note_id = $request->input('note_id');        
        $note_text = $request->input('note_text');

        if (!$user) {
            abort(400, trans('errors.invalid_input_data'));
        }

        if ($note_id > 0) {
            $note = \App\Models\Employee\Note::find($note_id);
        } else {
            $note = new \App\Models\Employee\Note();
        }

        $note->note = $note_text;
        $note->is_hr = $this->has_manager_access;
        $note->user_id = $user_id;

        if (!$note->created_user_id) {
            $note->created_time = new \DateTime();
            $note->created_user_id = \Auth::user()->id;
        }
        $note->modified_time = new \DateTime();
        $note->modified_user_id = \Auth::user()->id;

        $note->save();
        
        $view = view('profile.control_notes_record', [
            'note' => $note, 
            'is_new' => true,
            'has_hr_access' => $this->has_hr_access,
            'has_manager_access' => $this->has_manager_access
            ])->render();

        $result = [
            'view' => $view
        ];

        return $result;
    }

    /**
     * Get rights and check if user has access to employees notes
     * @param \App\User $user Users model
     */
    public function getAccess($user)
    {
        if (!$this->getListEditRights()) {
            return;
        }

        $this->has_hr_access = $this->getHRAccess();

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

        // Check if register exist for users notes
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
     * Validate if user has access to employee note tab. If not then request is aborted.
     */
    private function validateAccess()
    {
        if (!$this->has_hr_access && !$this->has_manager_access) {
            abort(403, trans('errors.no_rights_on_register'));
        }
    }
}

<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use App\Libraries\Rights;
use DB;

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
                    'has_manager_access' => $this->has_manager_access,
                    'users_who_see' => $this->whoCanSee($user)
                ])->render();
    }

    /**
     * Saves data
     * @param Request $request Data request
     * @return string Result
     */
    public function save(Request $request)
    {
        $this->validate($request, [
            'note_text' => 'required|min:1|max:2000'
        ]);

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
        $note->is_hr = $this->has_hr_access ? 1 : 0;
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
            'has_manager_access' => $this->has_manager_access,
            'users_who_see' => $this->whoCanSee($user)
                ])->render();

        $result = [
            'view' => $view
        ];

        return $result;
    }

    /**
     * Deletes note
     * @param Request $request Data request
     * @return string Result
     */
    public function delete(Request $request)
    {
        $note_id = $request->input('note_id');

        $note = \App\Models\Employee\Note::find($note_id);

        if (!$note) {
            abort(400, trans('notes.note_missing'));
        }

        // Retrieve user      
        $user = \App\User::find($note->user_id);

        // Check if user has edit rights to tab
        $this->getAccess($user);
        $this->validateAccess();

        $note->delete();

        return $note_id;
    }

    /**
     * Returns list of user who can see current note
     * @param \App\User $user Current user's model
     */
    private function whoCanSee($user)
    {
        $sql = "
            select 
                ur.user_id
            from 
                dx_users_roles ur 
                inner join dx_roles_lists rl on ur.role_id = rl.role_id 
            where 
                rl.list_id = :list_id AND
                rl.is_edit_rights = 1            
            ";

        $hr_users = DB::select($sql, array('list_id' => config('dx.employee_list_id')));

        $users_who_see = array();

        // Gets HR users
        foreach ($hr_users as $hr_user_row) {
            $hr_user = \App\User::find($hr_user_row->user_id);

            if ($hr_user) {
                $users_who_see[$hr_user->id] = array(
                    'is_manager' => false,
                    'id' => $hr_user->id,
                    'display_name' => $hr_user->display_name,
                    'position_title' => $hr_user->position_title,
                    'department' => ($hr_user->department ? $hr_user->department->title : ''),
                    'picture_guid' => $hr_user->picture_guid
                );
            }
        }

        // Gets manager
        $manager = \App\User::find($user->manager_id);

        if ($manager && !isset($users_who_see[$manager->id])) {
            $users_who_see[] = array(
                'is_manager' => true,
                'id' => $manager->id,
                'display_name' => $manager->display_name,
                'position_title' => $manager->position_title,
                'department' => ($manager->department ? $manager->department->title : ''),
                'picture_guid' => $manager->picture_guid
            );
        }

        return $users_who_see;
    }

    /**
     * Get rights and check if user has access to employees notes
     * @param \App\User $user Users model
     */
    public function getAccess($user)
    {
        $this->has_hr_access = $this->getHRAccess();

        // List edit rights are checked only for HR users. 
        // If user has manager access then it doesnt matter if he has access to list
        if (!$this->getListEditRights($user)) {
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
     * @param object $user User for which profile will be loaded
     * @return boolean True if has rights
     */
    private function getListEditRights($user)
    {
        $list = \App\Libraries\DBHelper::getListByTable('in_employees_notes');

        // Check if register exist for users notes
        if (!$list) {
            return false;
        }

        $list_rights = Rights::getRightsOnList($list->id);

        // Check if user has edit rights on list
        if (!($list_rights && $list_rights->is_edit_rights && $list_rights->is_edit_rights == 1)) {
            return 0;
        }
        
        return Rights::isSuperviseOnItem($user->dx_supervise_id);
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
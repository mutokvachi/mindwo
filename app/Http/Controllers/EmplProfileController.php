<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Libraries\Rights;
use DB;

/**
 * Employee profile controller
 */
class EmplProfileController extends Controller
{
        /**
         * Employee data for profile
         * 
         * @var object 
         */
	private $employee = null;
        
        /**
         * Indicates if new record is beeing created
         * 
         * @var boolean True - new item, False - editing 
         */
        private $is_new = false;
        
        /**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
                $this->is_new = true;
                
                $this->employee = new App\User;
		
		$form = new App\Libraries\Forms\Form(Config::get('dx.employee_list_id'));
		$form->disabled = false;
		$form->tabList = [
			trans('empl_profile.tab_general'),
			trans('empl_profile.tab_pdetails'),
			trans('empl_profile.tab_wdetails'),
			trans('empl_profile.tab_wplace'),
			trans('empl_profile.tab_cdetails'),
			trans('empl_profile.tab_addr')
		];
		$form->skipFields = ['picture_name'];
		
		return view('profile.employee', [
			'mode' => 'create',
			'employee' => $this->employee,
			'avail' => $this->employee->getAvailability(),
			'form' => $form,
			'is_my_profile' => false,
			'is_edit_rights' => $this->getEditRightsMode(),
			'has_users_documents_access' => $this->validateUsersDocumentsAccess()
		]);
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		// not needed at now
	}
	
	public function show($id = null)
	{
		if(!$id)
		{
			$id = Auth::user()->id;
		}
		
		$this->employee = App\User::find($id);
		
		$form = new App\Libraries\Forms\Form(Config::get('dx.employee_list_id'), $id);
		$form->disabled = true;
		$form->tabList = [
			trans('empl_profile.tab_general'),
			trans('empl_profile.tab_pdetails'),
			trans('empl_profile.tab_wdetails'),
			trans('empl_profile.tab_wplace'),
			trans('empl_profile.tab_cdetails'),
			trans('empl_profile.tab_addr')
		];
		$form->skipFields = ['picture_name'];
		
		return view('profile.employee', [
			'mode' => 'show',
			'employee' => $this->employee,
			'avail' => $this->employee->getAvailability(),
			'form' => $form,
			'is_my_profile' => $id == Auth::user()->id,
			'is_edit_rights' => $this->getEditRightsMode(),
			'has_users_documents_access' => $this->validateUsersDocumentsAccess(),
			'has_users_notes_access' => $this->validateUsersNotesAccess($this->employee),
			'has_users_timeoff_access' => $this->validateUsersTimeoffAccess($this->employee),
		]);
	}
	
	public function edit($id, Request $request)
	{
		$this->employee = App\User::find($id);
	}
	
	public function update(Request $request, $id)
	{
	}
	
	/**
	 * Get updated content of auxiliary blocks after profile save.
	 *
	 * @param $id
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function ajaxShowChunks($id)
	{
		$this->employee = App\User::find($id);
		
		$result = [
			'success' => 1,
			'chunks' => []
		];
		
		$form = new App\Libraries\Forms\Form(Config::get('dx.employee_list_id'), $id);
		$form->disabled = true;
		
		$result['chunks']['.dx-employee-panel'] = view('profile.panel', [
			'mode' => 'show',
			'employee' => $this->employee,
			'avail' => $this->employee->getAvailability(),
			'form' => $form,
			'is_my_profile' => $id == Auth::user()->id,
			'is_edit_rights' => $this->getEditRightsMode()
		])->render();
		
		$result['chunks']['.dx-employee-hired'] = view('profile.tile_hired', [
			'employee' => $this->employee,
		])->render();
		
		$result['chunks']['.dx-employee-manager'] = view('profile.tile_manager', [
			'employee' => $this->employee,
		])->render();
		
		return response($result);
	}
	
	/**
	 * Get content of a tab via AJAX request
	 *
	 * @param Request $request
	 * @param $id
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function ajaxShowTab(Request $request, $id)
	{
		$this->employee = App\User::find($id);
		$tabId = $request->input('tab_id');
		
		$result = [
			'success' => 1,
			'html' => ''
		];
		
		$result['html'] = view('profile.' . $tabId, [
			'mode' => 'show',
			'employee' => $this->employee,
			'is_my_profile' => $id == Auth::user()->id,
			'is_edit_rights' => $this->getEditRightsMode()
		])->render();
		
		return response($result);
	}
	
	/**
	 * Check if user has access for users time off data
	 * @return boolean Parameter if user has access
	 */
	private function validateUsersTimeoffAccess($user)
	{
		$user_timeoff_controller = new App\Http\Controllers\Employee\TimeoffController();
		
		$user_timeoff_controller->getAccess($user);
		
		return ($user_timeoff_controller->has_hr_access || $user_timeoff_controller->has_my_access);
	}
	
	/**
	 * Check if user has access for users documents
	 * @return boolean Parameter if user has access
	 */
	private function validateUsersDocumentsAccess()
	{
		$user_documents_controller = new App\Http\Controllers\Employee\EmployeePersonalDocController();
		return $user_documents_controller->has_access;
	}
	
	/**
	 * Check if user has access for users notes
	 * @param \App\User $user Employee's user model
	 * @return boolean Parameter if user has access
	 */
	private function validateUsersNotesAccess($user)
	{
		$user_notes_controller = new App\Http\Controllers\Employee\NoteController();
		
		$user_notes_controller->getAccess($user);
		
		return ($user_notes_controller->has_hr_access || $user_notes_controller->has_manager_access);
	}
	
	/**
	 * Checks if user have edit rights on employees register
	 * @return int 0 - no edit rights; 1 - can edit
	 */
	private function getEditRightsMode()
	{
		$empl_list_rights = Rights::getRightsOnList(Config::get('dx.employee_list_id'));
		
		if(!($empl_list_rights && $empl_list_rights->is_edit_rights))
		{
                    return 0; // no rights on employees list
		}
                
                if ($this->is_new) {
                    return 1; // new item
                }
                
                return Rights::isSuperviseOnItem($this->employee->dx_supervise_id);
                
	}
}

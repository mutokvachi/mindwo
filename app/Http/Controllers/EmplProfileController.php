<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Libraries\Rights;

/**
 * Employee profile controller
 */
class EmplProfileController extends Controller
{
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$employee = new App\User;
		
		$form = new App\Libraries\Forms\Form(Config::get('dx.employee_list_id'));
		$form->disabled = false;
		$form->tabList = ['General', 'Personal details', 'Work details', 'Workplace', 'Contact details', 'Addresses'];
		$form->skipFields = ['picture_name'];
		
		return view('profile.employee', [
			'mode' => 'create',
			'employee' => $employee,
			'avail' => $employee->getAvailability(),
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
		
		$employee = App\User::find($id);
		
		$form = new App\Libraries\Forms\Form(Config::get('dx.employee_list_id'), $id);
		$form->disabled = true;
		$form->tabList = ['General', 'Personal details', 'Work details', 'Workplace', 'Contact details', 'Addresses'];
		$form->skipFields = ['picture_name'];
		
		return view('profile.employee', [
			'mode' => 'show',
			'employee' => $employee,
			'avail' => $employee->getAvailability(),
			'form' => $form,
			'is_my_profile' => $id == Auth::user()->id,
			'is_edit_rights' => $this->getEditRightsMode(),
			'has_users_documents_access' => $this->validateUsersDocumentsAccess()
		]);
	}
	
	public function ajaxShowChunks($id)
	{
		$employee = App\User::find($id);
		
		$result = [
			'success' => 1,
			'chunks' => []
		];
		
		$form = new App\Libraries\Forms\Form(Config::get('dx.employee_list_id'), $id);
		$form->disabled = true;
		
		$result['chunks']['.dx-employee-panel'] = view('profile.panel', [
			'mode' => 'show',
			'employee' => $employee,
			'avail' => $employee->getAvailability(),
			'form' => $form,
			'is_my_profile' => $id == Auth::user()->id,
			'is_edit_rights' => $this->getEditRightsMode()
		])->render();
		
		$result['chunks']['.dx-employee-hired'] = view('profile.tile_hired', [
			'employee' => $employee,
		])->render();
		
		$result['chunks']['.dx-employee-manager'] = view('profile.tile_manager', [
			'employee' => $employee,
		])->render();
		
		return response($result);
	}
	
	public function edit($id, Request $request)
	{
		$employee = App\User::find($id);
	}
	
	public function update(Request $request, $id)
	{
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
	 * Checks if user have edit rights on employees register
	 * @return int 0 - no edit rights; 1 - can edit
	 */
	private function getEditRightsMode()
	{
		$empl_list_rights = Rights::getRightsOnList(Config::get('dx.employee_list_id'));
		
		$is_edit_rights = 0;
		if($empl_list_rights && $empl_list_rights->is_edit_rights)
		{
			$is_edit_rights = 1;
		}
		
		return $is_edit_rights;
	}
}

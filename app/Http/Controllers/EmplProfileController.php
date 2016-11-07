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
		
		return view('profile.employee', [
			'mode' => 'create',
			'employee' => $employee,
			'avail' => $employee->getAvailability(),
			'form' => $form,
			'is_my_profile' => false,
			'is_edit_rights' => $this->getEditRightsMode()
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
		
		return view('profile.employee', [
			'mode' => 'show',
			'employee' => $employee,
			'avail' => $employee->getAvailability(),
			'form' => $form,
			'is_my_profile' => $id == Auth::user()->id,
			'is_edit_rights' => $this->getEditRightsMode()
		]);
	}
	
	public function ajaxShowChunks($id)
	{
		$employee = App\User::find($id);

		$result = [];
		$result['panel'] = view('profile.panel', [
			'employee' => $employee,
			'avail' => $employee->getAvailability(),
			'is_my_profile' => $id == Auth::user()->id,
			'is_edit_rights' => $this->getEditRightsMode(),
                        'mode' => 'view'
		])->render();
		
		$result['manager'] = view('profile.tile_manager', [
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

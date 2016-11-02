<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * Employee profile controller
 */
class EmplProfileController extends Controller
{
	public function show($id = null)
	{
		if(!$id)
			$id = Auth::user()->id;
		
		$employee = App\User::find($id);
		
		$form = new App\Libraries\Forms\Form(Config::get('dx.employee_list_id'), $id);
		$form->disabled = true;
		$form->tabList = ['General', 'Personal details', 'Work details', 'Workplace', 'Contact details', 'Addresses'];
		
		return view('profile.employee', [
			'employee' => $employee,
			'avail' => $employee->getAvailability(),
			'form' => $form,
			'is_my_profile' => $id == Auth::user()->id,
		]);
	}
	
	public function edit($id, Request $request)
	{
		$employee = App\User::find($id);
		
	}
	
	public function update(Request $request, $id)
	{
		
	}
}

<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
		
		return view('profile.employee', [
			'employee' => $employee,
			'avail' => $employee->getAvailability(),
			'is_my_profile' => $id == Auth::user()->id,
		]);
	}
}

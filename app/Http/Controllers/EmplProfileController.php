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
                        'is_edit_rights' => $this->getEditRightsMode()
		]);
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
        private function getEditRightsMode() {
            $empl_list_rights = Rights::getRightsOnList(Config::get('dx.employee_list_id'));
                
            $is_edit_rights = 0;
            if ($empl_list_rights && $empl_list_rights->is_edit_rights) {
                $is_edit_rights = 1;
            }
            
            return $is_edit_rights;
        }
}

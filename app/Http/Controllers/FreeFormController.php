<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Webpatser\Uuid\Uuid;
use App\Libraries\FormField;
use Auth;
use Config;
use App\Exceptions;

/**
 * Class FreeFormController
 *
 * Free form - a concept of a form with an arbitrary view. Used for editing content in place, without rendering
 * a separate grid-based form.
 *
 * @package App\Http\Controllers
 */
class FreeFormController extends FormController
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		// not needed at now
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		// not needed at now
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
	
	/**
	 * Display the specified resource.
	 *
	 * Returns a JSON structure with actual values of current fieldset. Used for updating view after saving free form.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		//
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * Returns a JSON structure with input fields. Used for replacing static data with input fields when editing free
	 * form.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id, Request $request)
	{
		$result = [];
		$item_id = $request->input('item_id');
		$list_id = $request->input('list_id');
		$class = $request->input('model');
		$model = $class::find($id);
		
		$this->form_is_edit_mode = false;
		$parent_item_id = 0;
		$parent_field_id = 0;
		$params = $this->getFormParams($list_id);
		$this->checkUserRights($list_id, $item_id);
		$frm_uniq_id = Uuid::generate(4);
		$this->is_editable_wf = true; // we wont check workflow status here
		$row_data = $this->getFormItemDataRow($list_id, $item_id, $params);
		$fields = $this->getFormFields($params);
		
		$fieldset = [];
		
		foreach($request->input('fields') as $f)
		{
			$fieldset[] = $f['name'];
		}
		
		foreach($fields as $row)
		{
			if(!in_array($row->db_name, $fieldset))
			{
				continue;
			}
			
			$field = new FormField($row, $list_id, $item_id, $parent_item_id, $parent_field_id, $row_data,
				$frm_uniq_id);
			
			$html = $field->get_field_htm();
			
			$result['fields'][] = [
				'name' => $row->db_name,
				'input' => $html
			];
		}
		
		$result['success'] = 1;
		
		return response($result);
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * Updates model in storage after submitting free form.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		$result = [];
		
		$class = $request->input('model');
		$model = $class::find($id);
		$item_id = $request->input('item_id');
		$list_id = $request->input('list_id');
		$params = $this->getFormParams($list_id);
		$this->checkUserRights($list_id, $item_id);
		$this->is_editable_wf = true; // we wont check workflow status here
		$fields = $this->getFormFields($params);
		
		$fieldset = [];
		
		foreach($request->input('fields') as $f)
		{
			$fieldset[$f['name']] = $f['data'];
		}
		
		foreach($fields as $row)
		{
			if(!in_array($row->db_name, array_keys($fieldset)))
			{
				continue;
			}
			
			$name = $row->db_name;
			$model->$name = $fieldset[$name];
			
			$result['fields'][] = [
				'name' => $name,
				'html' => $model->$name
			];
		}
		
		$model->save();
		
		$result['success'] = 1;
		
		return response($result);
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		// not needed at now
	}
	
	/**
	 * Checks if user have rights to edit - user can edit only his own profile
	 *
	 * @param integer $list_id List ID (must be dx_users list)
	 * @param integer $item_id User ID
	 * @throws Exceptions\DXCustomException
	 */
	private function checkUserRights($list_id, $item_id)
	{
		
		if(Auth::user()->id != $item_id || $list_id != Config::get('dx.employee_list_id'))
		{
			throw new Exceptions\DXCustomException(trans('empl_profile.err_no_edit_rights'));
		}
		
		$this->form_is_edit_mode = 1;
		$this->is_disabled = 0;
		$this->is_edit_rights = 1;
	}
}

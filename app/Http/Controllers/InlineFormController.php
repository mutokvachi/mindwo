<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Libraries\FormField;
use App\Libraries\FormSave;
use App\Libraries\Rights;

use App\Http\Requests;

/**
 * Class InlineFormController
 *
 * Inline form - an AJAX form embedded into a page. This controller provides server-side support ot this kind of forms.
 *
 * @package App\Http\Controllers
 */
class InlineFormController extends FormController
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
		$this->validate($request, [
			'edit_form_id' => 'required|integer|exists:dx_forms,id',
			'item_id' => 'integer',
			'multi_list_id' => 'integer'
		]);
		
		$item_id = $request->input('item_id');
		$form_id = $request->input('edit_form_id');
		$url = $request->input('redirect_url');
		
		$this->checkSaveRights($form_id, $item_id);
		
		$save_obj = new FormSave($request);
		
		return response([
			'success' => 1,
			'redirect' => $url . $save_obj->item_id,
                        'item_id' => $save_obj->item_id
		]);
	}
	
	/**
	 * Display the specified resource.
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
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $id)
	{
		$list_id = $request->input('list_id');
		$tabList = $request->input('tab_list');
		
		$form = new \App\Libraries\Forms\Form($list_id, $id);
		$form->disabled = false;
		$form->tabList = $tabList;
		
		$tabs = $form->renderTabContents();
		
		$result = [
			'success' => 1,
			'tabs' => $tabs
		];
		
		return response($result);
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		$this->validate($request, [
			'edit_form_id' => 'required|integer|exists:dx_forms,id',
			'item_id' => 'integer',
			'multi_list_id' => 'integer'
		]);
		
		$item_id = $request->input('item_id');
		$list_id = $request->input('list_id');
		$form_id = $request->input('edit_form_id');
		$tabList = $request->input('tabList', []);
		
		$this->checkSaveRights($form_id, $item_id);
		
		$save_obj = new FormSave($request);
		
		$form = new \App\Libraries\Forms\Form($list_id, $item_id);
		$form->disabled = true;
		$form->tabList = $tabList;
		
		$tabs = $form->renderTabContents();
		
		$result = [
			'success' => 1,
			'tabs' => $tabs
		];
		
		return response($result);
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, $id)
	{
		$this->deleteItem($request);
		
		$result = [
			'redirect' => '/search',
			'success' => 1
		];
		
		return response($result);
	}
}

<?php

namespace App\Http\Controllers\Constructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GridController;
use App\Models\System\Form;
use App\Models\System\FormTab;
use App\Models\System\ListField;
use App\Models\System\ListRole;
use App\Models\System\Lists;
use App\Models\System\View;
use App\Models\System\ViewField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/**
 * Registers UI constructor controller
 */
class RegisterController extends Controller
{
	/**
	 * Current register ID being processed
	 * @var integer
	 */
	protected $id = 0;
	protected $view_id = 1;
	protected $list = null;
	protected $view = null;
	protected $form = null;
	protected $steps = ['names', 'columns', 'fields', 'rights', 'workflows'];
	
	/**
	 * RegisterController constructor.
	 */
	public function __construct()
	{
		$this->id = Route::current()->getParameter('id', 0);
		
		view()->share([
			'list_id' => $this->id,
			'list' => $this->getList(),
			'steps' => $this->steps
		]);
		
		if($this->getView())
		{
			view()->share([
				'view_id' => $this->getView()->id,
				'view' => $this->getView()
			]);
		}
		else
		{
			view()->share([
				'view_id' => 1
			]);
		}
		
		view()->share([
			'form_id' => $this->getForm() ? $this->getForm()->id : 0
		]);
	}
	
	/**
	 * Render first step in creation mode - register/item name, position of menu entry.
	 *
	 * @return string
	 */
	public function create()
	{
		$result = view('constructor.names', [
			'step' => 'names',
			'register_menu_field_htm' => $this->getMenuHtm(),
			'item_title' => ''
		])->render();
		
		return $result;
	}
	
	/**
	 * Store basic data of a register and create all related data structures.
	 *
	 * @param Request $request
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function store(Request $request)
	{
		$listName = $request->input('list_name');
		$itemName = $request->input('item_name');
		$menuParentID = $request->input('menu_parent_id', 0);
		
		$userId = Auth::user()->id;
		
		$rel_list_id = \App\Libraries\DBHelper::getListByTable('dx_lists')->id;
		$rel_fld_id = DB::table('dx_lists_fields')->where('list_id', '=', $rel_list_id)
			->where('db_name', '=', 'list_title')->first()->id;
		
		DB::transaction(function () use ($listName, $itemName, $userId, $menuParentID, $rel_list_id, $rel_fld_id) {
			$list = new Lists([
				'list_title' => $listName,
				'item_title' => $itemName,
				'object_id' => \App\Libraries\DBHelper::OBJ_DX_DOC,
				'created_user_id' => $userId,
				'modified_user_id' => $userId,
			]);
			
			$list->save();
			$this->id = $list->id;
			
			$this->saveMenuField($menuParentID, $list->id, $listName);
			
			$field = new ListField([
				'db_name' => 'id',
				'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_ID,
				'title_list' => 'ID',
				'title_form' => 'ID',
			]);
			
			$list->fields()->save($field);
			
			// Lists based on dx_doc table must have field list_id with default value and criteria so documents are seperated by registers
			$field_lst = new ListField([
				'db_name' => 'list_id',
				'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_RELATED,
				'title_list' => trans('db_dx_lists_fields.fld_list'),
				'title_form' => trans('db_dx_lists_fields.fld_list'),
				'rel_list_id' => $rel_list_id,
				'rel_display_field_id' => $rel_fld_id,
				'default_value' => $list->id,
				'operation_id' => \App\Libraries\DBHelper::FIELD_OPERATION_EQUAL,
				'criteria' => $list->id,
			]);
			
			$list->fields()->save($field_lst);
			
			$view = new View([
				'title' => $listName,
				'view_type_id' => 1,
				'is_default' => 1,
				'created_user_id' => $userId,
				'modified_user_id' => $userId,
			]);
			
			$list->views()->save($view);
			
			$form = new Form([
				'title' => $itemName,
				'form_type_id' => 1,
				'created_user_id' => $userId,
				'modified_user_id' => $userId,
			]);
			
			$list->form()->save($form);
			
			$form->fields()->create([
				'list_id' => $this->id,
				'field_id' => $field_lst->id,
				'order_index' => 0,
				'row_type_id' => 1,
				'created_user_id' => $userId,
				'modified_user_id' => $userId,
				'is_hidden' => 1
			]);
			
			$role = new ListRole([
				'role_id' => config('dx.constructor.access_role_id', 1),
				'is_edit_rights' => 1,
				'is_delete_rights' => 1,
				'is_new_rights' => 1,
				'is_import_rights' => 1,
				'is_view_rights' => 1,
				'created_user_id' => $userId,
				'modified_user_id' => $userId
			]);
			
			$list->roles_lists()->save($role);
			
			$view_fields = new ViewField([
				'list_id' => $list->id,
				'field_id' => $field->id,
				'order_index' => 10,
				'is_hidden' => 1
			]);
			
			$view->fields()->save($view_fields);
		});
		
		$result = [
			'success' => 1,
			'list_id' => $this->id
		];
		
		return response($result);
	}
	
	/**
	 * Render first step in editing mode.
	 *
	 * @param $id
	 * @return string
	 */
	public function edit($id)
	{
		$result = view('constructor.names', [
			'step' => 'names',
			'register_menu_field_htm' => $this->getMenuHtm(),
			'item_title' => $this->getItemTitle($this->id)
		])->render();
		
		return $result;
	}
	
	/**
	 * Update register name and item name.
	 *
	 * @param $id
	 * @param Request $request
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function update($id, Request $request)
	{
		$listName = $request->input('list_name');
		$itemName = $request->input('item_name');
		$menuParentID = $request->input('menu_parent_id', 0);
		
		$list = $this->getList();
		
		DB::transaction(function () use ($list, $listName, $itemName, $menuParentID) {
			$list->list_title = $listName;
			$list->item_title = $itemName;
			$list->save();
			
			$list->form->update(['title' => $itemName]);
			
			$this->saveMenuField($menuParentID, $list->id, $listName);
		});
		
		$result = [
			'success' => 1,
			'list_id' => $list->id
		];
		
		return response($result);
	}
	
	/**
	 * Render second step - view editor.
	 *
	 * @param $id
	 * @return string
	 */
	public function editColumns($id)
	{
		$htm = GridController::getViewEditFormHTMLByViewId($this->getView()->id);
		
		$result = view('constructor.columns', [
			'step' => 'columns',
			'htm' => $htm,
			'operations' => DB::table('dx_field_operations')->orderBy('title')->get()
		])->render();
		
		return $result;
	}
	
	/**
	 * Render third step - form editor.
	 *
	 * @param $id
	 * @return string
	 */
	public function editFields($id)
	{
		$formFields = $this
			->getList()
			->form
			->fields()
			->leftJoin('dx_lists_fields', 'dx_forms_fields.field_id', '=', 'dx_lists_fields.id')
			->where(function ($query) {
				if($this->list->object_id == \App\Libraries\DBHelper::OBJ_DX_DOC)
				{
					$query->where('dx_lists_fields.db_name', '!=', 'list_id');
				}
			})
			->orderBy('order_index')
			->get();
		
		$grids = [];
		$grid = [];
		$listFieldsIds = [];
		
		$tabs = $this->getList()->form->tabs()->orderBy('order_index')->get();
		
		$tabIds = [0];
		
		foreach($tabs as $tab)
		{
			if($tab->is_custom_data != 1)
			{
				continue;
			}
			
			$tabIds[] = $tab->id;
		}
		
		foreach($tabIds as $tabId)
		{
			$grid = [];
			$row = [];
			
			foreach($formFields as $field)
			{
				if($field->tab_id != $tabId)
				{
					continue;
				}
				
				$listFieldsIds[] = $field->field_id;
				
				if($field->row_type_id == 1)
				{
					// in case of errors in structure
					if(!empty($row))
					{
						$grid[] = $row;
						$row = [];
					}
					
					$grid[] = [$field];
				}
				else
				{
					$row[] = $field;
					
					if(count($row) == $field->row_type_id)
					{
						$grid[] = $row;
						$row = [];
					}
				}
			}
			
			$grids[$tabId] = $grid;
		}
		
		$listFields = $this
			->getList()
			->fields()
			->whereNotIn('id', $listFieldsIds)
			->where(function ($query) {
				if($this->list->object_id == \App\Libraries\DBHelper::OBJ_DX_DOC)
				{
					$query->where('db_name', '!=', 'list_id');
				}
			})
			->get();
		
		$result = view('constructor.fields', [
			'step' => 'fields',
			'listFields' => $listFields,
			'grid' => $grid,
			'grids' => $grids,
			'tabs' => $tabs
		])->render();
		
		return $result;
	}
	
	/**
	 * Save current state of a form being edited - order of tabs, positions of rows and fields.
	 *
	 * @param $id
	 * @param Request $request
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function updateFields($id, Request $request)
	{
		$userId = Auth::user()->id;
		$tabs = $request->input('tabs', []);
		$items0 = $request->input('items', []);
		
		$items = [];
		$label = null;
		
		// prepare form fields to be stored in db
		foreach($items0 as $tabId => $rows)
		{
			if(empty($rows))
			{
				continue;
			}
			
			$label = null;
			
			foreach($rows as $rowIndex => $row)
			{
				if(isset($row[0]['label']))
				{
					$label = trim($row[0]['label']);
					continue;
				}
				
				foreach($row as $itemIndex => $id)
				{
					$items[$id] = [
						'tab_id' => ($tabId > 0 ? $tabId : null),
						'order_index' => ($rowIndex + 1) . $itemIndex . '0',
						'group_label' => $label,
						'row_type_id' => count($row)
					];
					
					if($label)
					{
						$label = null;
					}
				}
			}
		}
		
		$form = $this->getList()->form;
		
		// For dx_docs in form must be included invisible field list_id
		$list_field_id = 0;
		
		if($this->list->object_id == \App\Libraries\DBHelper::OBJ_DX_DOC)
		{
			$list_field_id = DB::table('dx_lists_fields')->where('list_id', '=', $this->list->id)
				->where('db_name', '=', 'list_id')->first()->id;
		}
		
		DB::transaction(function () use ($form, $userId, $tabs, $items, $list_field_id) {
			// Update order of tabs
			foreach($tabs as $index => $tabId)
			{
				FormTab::where('id', $tabId)
					->update([
						'order_index' => ($index + 1) * 10
					]);
			}
			
			// Update form fields
			foreach($form->fields as $field)
			{
				// Update fields that already exist in the form
				if(isset($items[$field->field_id]))
				{
					$item = $items[$field->field_id];
					$field->tab_id = $item['tab_id'];
					$field->order_index = $item['order_index'];
					$field->group_label = $item['group_label'];
					$field->row_type_id = $item['row_type_id'];
					$field->modified_user_id = $userId;
					
					
					$field->save();
					
					unset($items[$field->field_id]);
				}
				// Delete fields that were excluded from the form
				else
				{
					if($field->field_id != $list_field_id)
					{
						$field->delete();
					}
				}
			}
			
			// Add new fields to the form
			foreach($items as $id => $item)
			{
				$form->fields()->create([
					'list_id' => $this->id,
					'field_id' => $id,
					'tab_id' => $item['tab_id'],
					'group_label' => $item['group_label'],
					'order_index' => $item['order_index'],
					'row_type_id' => $item['row_type_id'],
					'created_user_id' => $userId,
					'modified_user_id' => $userId,
				]);
			}
		});
		
		$result = [
			'success' => 1
		];
		
		return response($result);
	}
	
	/**
	 * Update single field with data submitted from modal dialog.
	 *
	 * @param $id
	 * @param Request $request
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function updateField($id, Request $request)
	{
		$fieldId = $request->input('field_id');
		$titleForm = $request->input('title_form');
		$isHidden = $request->input('is_hidden');
		
		DB::transaction(function () use ($fieldId, $titleForm, $isHidden) {
			$formField = $this->getList()->form->fields()->where('field_id', $fieldId)->first();
			$formField->is_hidden = $isHidden;
			$formField->save();
			
			$listField = $this->getList()->fields()->where('id', $formField->field_id)->first();
			$listField->title_form = $titleForm;
			$listField->save();
		});
		
		$result = [
			'success' => 1
		];
		
		return response($result);
	}
	
	/**
	 * Delete a tab.
	 *
	 * @param $id
	 * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
	 */
	public function deleteTab($id)
	{
		DB::transaction(function () use ($id) {
			FormTab::destroy($id);
		});
		
		$result = [
			'success' => 1
		];
		
		return response($result);
	}
	
	/**
	 * Render fourth step - access rights editor.
	 *
	 * @param $id
	 * @return string
	 */
	public function editRights($id)
	{
		$roles = $this->getList()->roles()->get();
		
		$result = view('constructor.rights', [
			'step' => 'rights',
			'roles' => $roles
		])->render();
		
		return $result;
	}
	
	/**
	 * Opens workflow edit view
	 *
	 * @param [integer] $id Lists's ID
	 * @return string HTML view
	 */
	public function editWorkflows($id)
	{
		$workflow = $this->getList()->workflows()->first();
		
		if($workflow)
		{
			$wf_cntrl = new \App\Http\Controllers\VisualWFController();
			
			$max_step = $wf_cntrl->getLastStep($workflow);
			
			if($max_step)
			{
				$max_step_nr = $max_step->step_nr;
			}
			else
			{
				$max_step_nr = 0;
			}
			
			$xml_data = $wf_cntrl->prepareXML($workflow->id);
		}
		else
		{
			$max_step_nr = 0;
			$xml_data = '';
		}
		
		$result = view('constructor.workflows', [
			'step' => 'workflows',
			'workflow' => $workflow,
			'xml_data' => $xml_data,
			'max_step_nr' => $max_step_nr,
			'wf_register_id' => $id
		])->render();
		
		return $result;
	}
	
	/**
	 * Updates workflows data
	 *
	 * @param [integer] $id List's ID
	 * @return void
	 */
	public function updateWorkflows($id)
	{
	
	}
	
	protected function getList()
	{
		if(!$this->list && $this->id)
		{
			$this->list = Lists::find($this->id);
		}
		
		return $this->list;
	}
	
	protected function getView()
	{
		if(!$this->view && $this->getList())
		{
			$this->view = $this->getList()->views()->where('is_default', 1)->first();
		}
		
		return $this->view;
	}
	
	protected function getForm()
	{
		if(!$this->form && $this->getList() && ($form = $this->getList()->form))
		{
			$this->form = $form;
		}
		
		return $this->form;
	}
	
	/**
	 * Returns register item title
	 *
	 * @param integer $list_id Register ID
	 * @return string
	 */
	private function getItemTitle($list_id)
	{
		$frm = $this->getList()->form;
		
		return ($frm) ? $frm->title : '';
	}
	
	/**
	 * Prepares HTML for menu field
	 *
	 * @return string
	 */
	private function getMenuHtm()
	{
		$list_id = \App\Libraries\DBHelper::getListByTable("dx_menu")->id;
		$form_id = DB::table('dx_forms')->where('list_id', '=', $list_id)->first()->id;
		$field_id = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'parent_id')
			->first()->id;
		
		$curent_menu_id = 0;
		$curent_menu = DB::table('dx_menu')->where('list_id', '=', $this->id)->first();
		
		if($curent_menu && $curent_menu->parent_id)
		{
			$curent_menu_id = $curent_menu->parent_id;
		}
		
		$fld_attr = \App\Libraries\DBHelper::getFormFields($form_id, $field_id)[0];
		
		$fld = new \App\Libraries\FieldsHtm\FieldHtm_tree($fld_attr, 0, $curent_menu_id, $list_id,
			"register_menu_parent", 0, 1);
		
		return $fld->getHtm();
	}
	
	/**
	 * Creates or updates register menu item
	 *
	 * @param integer $menuParentID Parent menu item ID. If 0 then no menu
	 * @param integer $list_id Register ID for which menu item is updated/created
	 * @param string $listName Register title
	 */
	private function saveMenuField($menuParentID, $list_id, $listName)
	{
		$curent_menu = DB::table('dx_menu')->where('list_id', '=', $list_id)->first();
		
		if($curent_menu)
		{
			if($curent_menu->parent_id != intval($menuParentID) || $curent_menu->title != $listName)
			{
				if($menuParentID)
				{
					DB::table('dx_menu')
						->where('id', '=', $curent_menu->id)
						->update([
							'parent_id' => $menuParentID,
							'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $menuParentID)
									->max('order_index') + 10),
							'title' => $listName,
						]);
				}
				else
				{
					DB::table('dx_menu')
						->where('id', '=', $curent_menu->id)
						->delete();
				}
			}
		}
		else
		{
			if($menuParentID)
			{
				DB::table('dx_menu')->insert([
					'parent_id' => $menuParentID,
					'title' => $listName,
					'list_id' => $list_id,
					'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $menuParentID)
							->max('order_index') + 10),
					'group_id' => 1,
					'position_id' => 1
				]);
			}
		}
	}
}

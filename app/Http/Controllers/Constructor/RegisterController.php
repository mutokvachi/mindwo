<?php

namespace App\Http\Controllers\Constructor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GridController;
use App\Models\System\Form;
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
	 * Current register ID beeing processed
	 * @var integer
	 */
	protected $id = 0;
	protected $view_id = 1;
	protected $list = null;
	protected $view = null;
	protected $steps = ['names', 'columns', 'fields', 'rights', 'menu'];
	
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
	}
	
	public function index()
	{
	}
	
	public function create()
	{
		$result = view('constructor.names', [
			'step' => 'names'
		])->render();
		
		return $result;
	}
	
	public function store(Request $request)
	{
		$listName = $request->input('list_name');
		$itemName = $request->input('item_name');
		$userId = Auth::user()->id;
		
		DB::transaction(function () use ($listName, $itemName, $userId)
		{
			$list = new Lists([
				'list_title' => $listName,
				'item_title' => $itemName,
				'object_id' => 140,
				'created_user_id' => $userId,
				'modified_user_id' => $userId,
			]);
			
			$list->save();
			$this->id = $list->id;
			
			$field = new ListField([
				'db_name' => 'id',
				'type_id' => 6,
				'title_list' => 'ID',
				'title_form' => 'ID',
			]);
			
			$list->fields()->save($field);
			
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
	
	public function edit($id)
	{
		$result = view('constructor.names', [
			'step' => 'names',
		])->render();
		
		return $result;
	}
	
	public function update($id, Request $request)
	{
		$listName = $request->input('list_name');
		$itemName = $request->input('item_name');
		$list = $this->getList();
		
		DB::transaction(function () use ($listName, $itemName, $list)
		{
			$list->list_title = $listName;
			$list->item_title = $itemName;
			$list->save();
		});
		
		$result = [
			'success' => 1,
			'list_id' => $list->id
		];
		
		return response($result);
	}
	
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
	
	public function updateColumns($id)
	{
	}
	
	public function editFields($id)
	{
		$formFields
			= $this
			->getList()
			->form
			->fields()
			->leftJoin('dx_lists_fields', 'dx_forms_fields.field_id', '=', 'dx_lists_fields.id')
			->orderBy('order_index')
			->get();
		
		$grid = [];
		$row = [];
		$listFieldsIds = [];
		
		foreach($formFields as $field)
		{
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
		
		$listFields = $this
			->getList()
			->fields()
			->whereNotIn('id', $listFieldsIds)
			->get();
		
		$result = view('constructor.fields', [
			'step' => 'fields',
			'listFields' => $listFields,
			'grid' => $grid
		])->render();
		
		return $result;
	}
	
	public function updateFields($id, Request $request)
	{
		$userId = Auth::user()->id;
		$items0 = $request->input('items', []);
		$items = [];
		
		foreach($items0 as $rowIndex => $row)
		{
			foreach($row as $itemIndex => $id)
			{
				$items[$id] = [
					'order_index' => ($rowIndex + 1) . $itemIndex . '0',
					'row_type_id' => count($row)
				];
			}
		}
		
		$form = $this->getList()->form;
		
		DB::transaction(function () use ($form, $items, $userId)
		{
			foreach($form->fields as $field)
			{
				if(isset($items[$field->field_id]))
				{
					$item = $items[$field->field_id];
					$field->order_index = $item['order_index'];
					$field->row_type_id = $item['row_type_id'];
					$field->modified_user_id = $userId;
					$field->save();
					
					unset($items[$field->field_id]);
				}
				
				else
				{
					$field->delete();
				}
			}
			
			foreach($items as $id => $item)
			{
				$form->fields()->create([
					'list_id' => $this->id,
					'field_id' => $id,
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
	
	public function updateField($id, Request $request)
	{
		$fieldId = $request->input('field_id');
		$titleForm = $request->input('title_form');
		$isHidden = $request->input('is_hidden');
		
		DB::transaction(function () use ($fieldId, $titleForm, $isHidden)
		{
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
	
	public function editRights($id)
	{
		$roles = $this->getList()->roles()->get();
		
		$result = view('constructor.rights', [
			'step' => 'rights',
			'roles' => $roles
		])->render();
		
		return $result;
	}
	
	public function updateRights($id)
	{
	}
	
	public function editMenu($id)
	{
		$result = view('constructor.menu', [
			'step' => 'menu',
		])->render();
		
		return $result;
	}
	
	public function updateMenu($id)
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
}

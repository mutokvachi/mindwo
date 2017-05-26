<?php

namespace App\Http\Controllers\Constructor;

use App\Http\Controllers\Controller;
use App\Models\System\Form;
use App\Models\System\Lists;
use App\Models\System\View;
use App\Libraries\Blocks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
	
	public function __construct()
	{
		$this->id = Route::current()->getParameter('id', 0);
		
		view()->share([
			'list_id' => $this->id,
			'view_id' => $this->view_id,
			'list' => $this->getList()
		]);
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
		
		$list = new Lists([
			'list_title' => $listName,
			'item_title' => $itemName,
			'object_id' => 140,
			'created_user_id' => $userId,
			'modified_user_id' => $userId,
		]);
		
		$list->save();
		
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
		
		$result = [
			'success' => 1,
			'list_id' => $list->id
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
		$list->list_title = $listName;
		$list->item_title = $itemName;
		$list->save();
		
		$result = [
			'success' => 1,
			'list_id' => $list->id
		];
		
		return response($result);
	}
	
	public function editColumns($id)
	{
		$result = view('constructor.columns', [
			'step' => 'columns',
			'fields' => $this->getList()->fields()->get(),
			'formFields' => $this->getList()->form->fields()->orderBy('order_index')->get(),
		])->render();
		
		return $result;
	}
	
	public function updateColumns($id)
	{
	}
	
	public function editFields($id)
	{
		$result = view('constructor.fields', [
			'step' => 'fields',
			'fields' => $this->getList()->fields()->get(),
			'formFields' => $this->getList()->form->fields()->orderBy('order_index')->get(),
		])->render();
		
		return $result;
	}
	
	public function updateFields($id, Request $request)
	{
		$items0 = $request->input('items', []);

		$userId = Auth::user()->id;

		$items = [];
		foreach($items0 as $item0)
		{
			$items[$item0['id']] = [
				'col' => $item0['col'],
				'row' => $item0['row'],
			];
		}
		
		$form = $this->getList()->form;
		
		foreach($form->fields as $field)
		{
			if(isset($items[$field->field_id]))
			{
				$item = $items[$field->field_id];
				$field->col_number = $item['col'];
				$field->row_number = $item['row'];
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
				'order_index' => $item['row'].$item['col'].'0',
				'row_number' => $item['row'],
				'col_number' => $item['col'],
				'created_user_id' => $userId,
				'modified_user_id' => $userId,
			]);
		}
		
		$result = [
			'success' => 1
		];
		
		return response($result);
	}
	
	public function editRights($id)
	{
		$result = view('constructor.rights', [
			'step' => 'rights',
			'rolesHTML' => $this->getRolesHtml()
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
	
	public function getRolesHtml()
	{
		$block_grid = Blocks\BlockFactory::build_block("OBJ=VIEW|VIEW_ID=25");
		$block_grid->rel_field_id = 105;
		$block_grid->rel_field_value = $this->id;
		return $block_grid->getHTML();
	}
}

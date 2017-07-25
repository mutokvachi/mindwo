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
    protected $steps = ['names', 'columns', 'fields', 'rights', 'workflows'];
    
    public function __construct()
    {
        $this->id = Route::current()->getParameter('id', 0);

        view()->share([
            'list_id' => $this->id,
            'list' => $this->getList(),
            'steps' => $this->steps            
        ]);

        if ($this->getView()) {
            view()->share([
                'view_id' => $this->getView()->id,
                'view' => $this->getView()
            ]);
        }
        else {
            view()->share([
                'view_id' => 1
            ]);
        }
    }
    
    /**
     * Returns register item title
     * 
     * @param integer $list_id Register ID
     * @return string
     */
    private function getItemTitle($list_id) {
        $frm = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
        
        return ($frm) ? $frm->title : '';
    }

    public function index()
    {
        
    }

    public function create()
    {
        $result = view('constructor.names', [
            'step' => 'names',
            'register_menu_field_htm' => $this->getMenuHtm(),
            'item_title' => ''
        ])->render();

        return $result;
    }

    public function store(Request $request)
    {
        $listName = $request->input('list_name');
        $itemName = $request->input('item_name');
        $menuParentID = $request->input('menu_parent_id', 0);
        
        $userId = Auth::user()->id;

        $rel_list_id = \App\Libraries\DBHelper::getListByTable('dx_lists')->id;
        $rel_fld_id = DB::table('dx_lists_fields')->where('list_id', '=', $rel_list_id)->where('db_name', '=', 'list_title')->first()->id;
        
        DB::transaction(function () use ($listName, $itemName, $userId, $menuParentID, $rel_list_id, $rel_fld_id)
        {
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
     * Prepares HTML for menu field
     * 
     * @return string
     */
    private function getMenuHtm()
    {
        $list_id = \App\Libraries\DBHelper::getListByTable("dx_menu")->id;
        $form_id = DB::table('dx_forms')->where('list_id', '=', $list_id)->first()->id;
        $field_id = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'parent_id')->first()->id;

        $curent_menu_id = 0;
        $curent_menu = DB::table('dx_menu')->where('list_id', '=', $this->id)->first();

        if ($curent_menu && $curent_menu->parent_id) {
            $curent_menu_id = $curent_menu->parent_id;
        }

        $fld_attr = \App\Libraries\DBHelper::getFormFields($form_id, $field_id)[0];

        $fld = new \App\Libraries\FieldsHtm\FieldHtm_tree($fld_attr, 0, $curent_menu_id, $list_id, "register_menu_parent", 0, 1);
        return $fld->getHtm();
    }

    public function update($id, Request $request)
    {
        $listName = $request->input('list_name');
        $itemName = $request->input('item_name');
        $menuParentID = $request->input('menu_parent_id', 0);

        DB::beginTransaction();

        try {
            $list = $this->getList();
            $list->list_title = $listName;
            $list->item_title = $itemName;
            $list->save();

            $this->saveMenuField($menuParentID, $list->id, $listName);
            
            DB::table('dx_forms')->where('list_id', '=', $this->id)->update(['title' => $itemName]);
            
            DB::commit();

            $result = [
                'success' => 1,
                'list_id' => $list->id
            ];
        }
        catch (\Exception $exception) {
            DB::rollBack();

            $result = [
                'success' => 0,
                'message' => trans('constructor.error_transaction')
            ];
        }

        return response($result);
    }
    
    /**
     * Creates or updates register menu item
     * 
     * @param integer $menuParentID Parent menu item ID. If 0 then no menu
     * @param integer $list_id Register ID for which menu item is updated/created
     * @param string $listName Register title
     */
    private function saveMenuField($menuParentID, $list_id, $listName) {
        $curent_menu = DB::table('dx_menu')->where('list_id', '=', $list_id)->first();
                
        if ($curent_menu) {
            if ($curent_menu->parent_id != intval($menuParentID) || $curent_menu->title != $listName) {
                if ($menuParentID) {
                    DB::table('dx_menu')
                            ->where('id', '=', $curent_menu->id)
                            ->update([
                                'parent_id' => $menuParentID,
                                'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $menuParentID)->max('order_index')+10),
                                'title' => $listName,
                    ]);
                }
                else {
                    DB::table('dx_menu')
                            ->where('id', '=', $curent_menu->id)
                            ->delete();
                }
            }
        }
        else {
            if ($menuParentID) {
                DB::table('dx_menu')->insert([
                    'parent_id' => $menuParentID,
                    'title' => $listName,
                    'list_id' => $list_id,
                    'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $menuParentID)->max('order_index')+10),
                    'group_id' => 1,
                    'position_id' => 1
                ]);
            }
        }
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
        $formFields = $this
                ->getList()
                ->form
                ->fields()
                ->leftJoin('dx_lists_fields', 'dx_forms_fields.field_id', '=', 'dx_lists_fields.id')
                ->where(function($query) {
                            if ($this->list->object_id == \App\Libraries\DBHelper::OBJ_DX_DOC) {
                                $query->where('dx_lists_fields.db_name', '!=', 'list_id');
                            }
                })
                ->orderBy('order_index')
                ->get();

        $grid = [];
        $row = [];
        $listFieldsIds = [];

        foreach ($formFields as $field) {
            $listFieldsIds[] = $field->field_id;

            if ($field->row_type_id == 1) {
                // in case of errors in structure
                if (!empty($row)) {
                    $grid[] = $row;
                    $row = [];
                }

                $grid[] = [$field];
            }
            else {
                $row[] = $field;

                if (count($row) == $field->row_type_id) {
                    $grid[] = $row;
                    $row = [];
                }
            }
        }

        $listFields = $this
                ->getList()
                ->fields()
                ->whereNotIn('id', $listFieldsIds)
                ->where(function($query) {
                            if ($this->list->object_id == \App\Libraries\DBHelper::OBJ_DX_DOC) {
                                $query->where('db_name', '!=', 'list_id');
                            }
                })
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

        foreach ($items0 as $rowIndex => $row) {
            foreach ($row as $itemIndex => $id) {
                $items[$id] = [
                    'order_index' => ($rowIndex + 1) . $itemIndex . '0',
                    'row_type_id' => count($row)
                ];
            }
        }

        $form = $this->getList()->form;

        // For dx_docs in form must be included invisible field list_id
        $list_field_id = 0;
        
        if ($this->list->object_id == \App\Libraries\DBHelper::OBJ_DX_DOC) {
            $list_field_id = DB::table('dx_lists_fields')->where('list_id', '=', $this->list->id)->where('db_name', '=', 'list_id')->first()->id;
        }
        
        DB::beginTransaction();

        try {

            foreach ($form->fields as $field) {
                if (isset($items[$field->field_id])) {
                    $item = $items[$field->field_id];
                    $field->order_index = $item['order_index'];
                    $field->row_type_id = $item['row_type_id'];
                    $field->modified_user_id = $userId;
                    $field->save();

                    unset($items[$field->field_id]);
                }
                else {                    
                    if ($field->field_id != $list_field_id) {
                        $field->delete();
                    }
                }
            }

            foreach ($items as $id => $item) {
                $form->fields()->create([
                    'list_id' => $this->id,
                    'field_id' => $id,
                    'order_index' => $item['order_index'],
                    'row_type_id' => $item['row_type_id'],
                    'created_user_id' => $userId,
                    'modified_user_id' => $userId,
                ]);
            }

            DB::commit();

            $result = [
                'success' => 1
            ];
        }
        catch (\Exception $exception) {
            DB::rollBack();

            $result = [
                'success' => 0,
                'message' => trans('constructor.error_transaction')
            ];
        }

        return response($result);
    }

    public function updateField($id, Request $request)
    {
        $fieldId = $request->input('field_id');
        $titleForm = $request->input('title_form');
        $isHidden = $request->input('is_hidden');

        DB::beginTransaction();

        try {
            $formField = $this->getList()->form->fields()->where('field_id', $fieldId)->first();
            $formField->is_hidden = $isHidden;
            $formField->save();

            $listField = $this->getList()->fields()->where('id', $formField->field_id)->first();
            $listField->title_form = $titleForm;
            $listField->save();

            DB::commit();

            $result = [
                'success' => 1
            ];
        }
        catch (\Exception $exception) {
            DB::rollBack();

            $result = [
                'success' => 0,
                'message' => trans('constructor.error_transaction')
            ];
        }

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

    /**
     * Opens workflow edit view
     *
     * @param [integer] $id Lists's ID
     * @return string HTML view
     */
    public function editWorkflows($id)
    {
        $workflow = $this->getList()->workflows()->first();

        $wf_cntrl = new App\Http\Controllers\VisualWFCOntroller();

        $max_step = $wf_cntrl->getLastStep($workflow);
        $xml_data = $wf_cntrl->prepareXML($workflow_id);

        /*
         data-xml_data = '{{ $xml_data }}' 
     data-max_step_nr="{{ $max_step_nr }}"
        */

        $result = view('constructor.workflows', [
            'step' => 'workflows',
            'workflow' => $workflow,
            'workflow' => $workflow,
            'xml_data' => $xml_data,
            'max_step_nr' => $max_step_nr
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
        if (!$this->list && $this->id) {
            $this->list = Lists::find($this->id);
        }

        return $this->list;
    }

    protected function getView()
    {
        if (!$this->view && $this->getList()) {
            $this->view = $this->getList()->views()->where('is_default', 1)->first();
        }

        return $this->view;
    }

}

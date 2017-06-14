<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use DB;
use App\Exceptions;
use Webpatser\Uuid\Uuid;
use App\Libraries\FormField;
use App\Libraries\FormSave;
use App\Libraries\Rights;
use Config;
use App\Models;
use PDO;
use App\Libraries\Workflows;

use Log;

class FormController extends Controller
{
    /**
     * Formas kontrolieris
     * 
     * Formas HTML attēlošana, datu saglabāšana, dzēšana
     */

    /**
     * Formas attēlošanas režīms - skatīšanās vai rediģēšanas.
     * Pēc noklusēšanas ir skatīšanās režīms. 
     * 
     * @var integer
     */
    protected $is_disabled = 1;

    /**
     * Pazīme, vai forma pieļauj dzēšanu (0 - nav, 1 - ir)
     * 
     * @var integer 
     */
    protected $is_delete_rights = 0;

    /**
     * Pazīme, vai forma pieļauj rediģēšanu (0 - jā, 1 - nē)
     * 
     * @var integer 
     */
    protected $is_edit_rights = 0;

    /**
     * Pazīme, vai ieraksta neatrodas darbplūsmā un ja atrodas, vai lietotājam ir rediģēšanas uzdevums, kas pieļauj rediģēt šo ierakstu
     * 
     * @var boolean
     */
    protected $is_editable_wf = true;
    
    /**
     * Pazīme, vai forma pieļauj rediģēšanu ( 0 - jā, 1 - nē)
     * @var integer 
     */
    protected $form_is_edit_mode = 0;
    
    /**
     * Row with last rejected task data
     * 
     * @var object
     */
    public $reject_task = null;

    /**
     * Formai definētā aktuālā darbplūsma - no tabulas dx_workflows_def
     * @var object 
     */
    private $workflow = null;
    
    /**
     * Array with data tabs HTMLs
     * @var array 
     */
    private $arr_data_tabs = [];
    
    /**
     * Indicates if workflow for this item can be canceled
     * 
     * @var boolean
     */
    public $is_wf_cancelable = false;
    
    /**
     * Rights on list
     * 
     * @var object 
     */
    public $list_right = null;
    
    /**
     * Unlocks item so other users can edit it
     * 
     * @param integer $list_id Register ID
     * @param integer $item_id Item ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function unlockItem($list_id, $item_id) {
        \App\Libraries\DBHelper::unlockItem($list_id, $item_id);
        return response()->json(['success' => 1]);
    }
    
     /**
     * Locks item so other users can't edit it
     * 
     * @param integer $list_id Register ID
     * @param integer $item_id Item ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function lockItem($list_id, $item_id) {
        
        $this->setFormsRightsMode($list_id, $item_id);
        
        \App\Libraries\DBHelper::lockItem($list_id, $item_id);
        
        return response()->json(['success' => 1]);
    }
    
    /**
     * Izgūst formas HTML ar AJAX pieprasījumu
     * 
     * @param \Illuminate\Http\Request $request POST pieprasījuma objekts
     * @return \Illuminate\Http\JsonResponse Atgriež formas HTML JSON izteiksmē
     */
    public function getForm(Request $request)
    {
        $this->validate($request, [
            'list_id' => 'required|integer|exists:dx_lists,id',
            'item_id' => 'required|integer'
        ]);

        // Must have parameters for form loading
        $item_id = $request->input('item_id', 0); // if 0 then new item creation form will be provided otherwise editing form
        $list_id = $request->input('list_id', 0);
        
        $this->form_is_edit_mode = $request->input('form_is_edit_mode', 0);
                
        $parent_item_id = $request->input('parent_item_id', 0);
        $parent_field_id = $request->input('parent_field_id', 0);

        $params = $this->getFormParams($list_id);

        $this->setFormsRightsMode($list_id, $item_id);

        $frm_uniq_id = Uuid::generate(4);
        $tab_id = Uuid::generate(4);
        
        $this->is_editable_wf = Rights::getIsEditRightsOnItem($list_id, $item_id); // Pārbauda vai nav darplūsmā un nav pabeigts
        
        // check if not locked by other user
        if ($this->form_is_edit_mode && $item_id > 0) {
            \App\Libraries\DBHelper::lockItem($list_id, $item_id);
        }
        
        $fields_htm = $this->getFormFieldsHTML($frm_uniq_id, $list_id, $item_id, $parent_item_id, $parent_field_id, $params);

        $this->setWorkflow($list_id);
        
        $table_name = \App\Libraries\Workflows\Helper::getListTableName($list_id);              
                
        $form_blade = ($params->is_full_screen_mode && $parent_item_id == 0) ? "form_full" : "form";
                
        $form_htm = view('elements.' . $form_blade, [
            'frm_uniq_id' => $frm_uniq_id,
            'form_title' => $params->form_title,
            'fields_htm' => $fields_htm,
            'tab_id' => $tab_id,
            'tabs_htm' => $this->getFormTabsHTML($tab_id, $params, $frm_uniq_id, $item_id),
            'form_id' => $params->form_id,
            'grid_htm_id' => $request->input('grid_htm_id', ''), // Ja uzstādīts, tad forma atvērta no grida
            'list_id' => $list_id,
            'item_id' => $item_id,
            'parent_field_id' => $parent_field_id,
            'parent_item_id' => $parent_item_id,
            'is_multi_registers' => $params->is_multi_registers,
            'js_code' => DB::table('dx_forms_js')->where('form_id', '=', $params->form_id)->get(), // Formai norādītie JavaScript
            'js_form_id' => str_replace("-", "_", $frm_uniq_id), // Formas GUID bez svītriņām, izmantojams JavaScript funkcijās kā mainīgais
            
             // if form is related to lookup or dropdown field from parent form
            'call_field_htm_id' => $request->input('call_field_htm_id', ''),
            'call_field_type' => $request->input('call_field_type', ''),
            'call_field_id' => $request->input('call_field_id', 0),
            
            'parent_form_htm_id' => $request->input('parent_form_htm_id', ''),
            'form_badge' => $this->getFormBadge($item_id, $this->form_is_edit_mode),
            'is_form_reloaded' => $request->input('is_form_reloaded', 0),
            'form_width' => $params->width,
            // Pogu pieejamība un rediģēšanas režīms
            'form_is_edit_mode' => $this->form_is_edit_mode,
            'is_disabled' => $this->is_disabled,
            'is_edit_rights' => $this->is_edit_rights,
            'is_delete_rights' => $this->is_delete_rights,
            'is_info_tasks_rights' => ($table_name == "dx_doc"),
            'workflow_btn' => $this->isWorkflowInit($list_id, $item_id), // Uzstāda pazīmi, vai redzama darbplūsmu poga 
            'is_custom_approve' => ($this->workflow && $this->workflow->is_custom_approve) ? 1 : 0,
            'is_editable_wf' => $this->is_editable_wf,
            'is_word_generation_btn' => \App\Libraries\Helper::getWordGenerBtn($list_id),
            'info_tasks' => \App\Libraries\Helper::getInfoTasks($list_id, $item_id, $table_name),
            'is_workflow_defined' => ($this->workflow),
            'self' => $this,
            'history_count' => DB::table('dx_db_events')->where('list_id', '=', $list_id)->where('item_id', '=', $item_id)->count()
        ])->render();
        
        return response()->json(['success' => 1, 'frm_uniq_id' => "" . $frm_uniq_id, 'html' => $form_htm, 'is_fullscreen' => $params->is_full_screen_mode]);
    }
    
    /**
     * Returns all non-system roles
     * 
     * @return object Array with roles (table dx_roles)
     */
    public function getRoles() {
        return DB::table('dx_users_roles as ur')
                 ->join('dx_roles as r', 'r.id', '=', 'ur.role_id')
                 ->select('r.id', 'r.title', DB::raw('COUNT(*) as total_users'))
                 ->where('r.is_system', '=', '0')
                 ->groupBy('r.id')
                 ->orderBy('r.title')
                 ->get();
    }
    
    /**
     * Returns JSON with item history activities
     * 
     * @param \Illuminate\Http\Request $request
     * @return Response JSON with history HTML
     */
    public function getItemHistory(Request $request) {
        $this->validate($request, [
            'list_id' => 'required|integer|exists:dx_lists,id',
            'item_id' => 'required|integer'
        ]);

        // Must have parameters for form loading
        $item_id = $request->input('item_id', 0); // if 0 then new item creation form will be provided otherwise editing form
        $list_id = $request->input('list_id', 0);
                   
        $result = view('elements.form_history', [
			'events' => Models\Event::where('list_id', '=', $list_id)->where('item_id', '=', $item_id)->orderBy('id', 'desc')->get(),
                        'is_profile' => (Config::get('dx.employee_profile_page_url')),
                        'events_list_id' => \App\Libraries\DBHelper::getListByTable('dx_db_events')->id
	])->render();

        return response()->json(['success' => 1, 'html' => $result]);
    }

    /**
     * Atgriež atjauninātus formas laukus
     * 
     * @param \Illuminate\Http\Request $request POST pieprasījuma objekts (AJAX pieprasījumi)
     * @return Response Atgriež formas HTML JSON izteiksmē
     */
    public function refreshFormFields(Request $request)
    {
        $this->validate($request, [
            'list_id' => 'required|integer|exists:dx_lists,id',
            'item_id' => 'required|integer',
            'frm_uniq_id' => 'required'
        ]);

        // Must have parameters for form loading
        $item_id = $request->input('item_id', 0); // if 0 then new item creation form will be provided otherwise editing form
        $list_id = $request->input('list_id', 0);

        $this->form_is_edit_mode = $request->input('form_is_edit_mode', 0);

        $parent_item_id = $request->input('parent_item_id', 0);
        $parent_field_id = $request->input('parent_field_id', 0);

        $frm_uniq_id = $request->input('frm_uniq_id', '');

        $params = $this->getFormParams($list_id);

        $this->setFormsRightsMode($list_id, $item_id);

        $fields_htm = $this->getFormFieldsHTML($frm_uniq_id, $list_id, $item_id, $parent_item_id, $parent_field_id, $params);

        return response()->json([
            'success' => 1, 
            'frm_uniq_id' => "" . $frm_uniq_id, 
            'html' => $fields_htm, 
            'tabs' => $this->arr_data_tabs, 
            'is_fullscreen' => $params->is_full_screen_mode,
            'history_count' => DB::table('dx_db_events')->where('list_id', '=', $list_id)->where('item_id', '=', $item_id)->count()
        ]);
    }

    /**
     * Izgūst saistītās izkrītošās izvēlnes vērtības
     * 
     * @param \Illuminate\Http\Request $request AJAX POST pieprasījums
     * @return Response JSON ar izkrītošās izvēlnes vērtībām
     */
    public function getBindedFieldData(Request $request)
    {
        $this->validate($request, [
            'binded_field_id' => 'required|integer|exists:dx_lists_fields,id',
            'binded_rel_field_id' => 'required|integer|exists:dx_lists_fields,id',
            'binded_rel_field_value' => 'required|integer'
        ]);

        $binded_field_id = $request->input('binded_field_id');
        $binded_rel_field_id = $request->input('binded_rel_field_id');
        $binded_rel_field_value = $request->input('binded_rel_field_value');

        return response()->json(['success' => 1, 'data' => $this->getBindedFieldsItems($binded_field_id, $binded_rel_field_id, $binded_rel_field_value)]);
    }

    /**
     * Save file edited by local application (Word, Excel etc)
     * This method works without authorization (by guid)
     * 
     * @param \Illuminate\Http\Request $request
     * @return Response Saved data info in JSON format
     * @throws Exceptions\DXCustomException
     */
    public function saveFile(Request $request) {        
        
        /*
        $this->validate($request, [
            'download_guid' => 'required|exists:dx_downloads,guid',
        ]);
        */
        $guid = $request->input('download_guid');
        
        $down = DB::table('dx_downloads')->where('guid', '=', $guid)->first();
        if (!$down) {
            throw new Exceptions\DXCustomException(sprintf(trans('errors.file_record_not_found'), $guid));
        }
        
        $file_field = DB::table('dx_lists_fields')->where('id', '=', $down->field_id)->first();
        
        Auth::loginUsingId($down->user_id, true);
        DB::table('dx_downloads')->where('id', '=', $down->id)->update(['last_download_time' => date('Y-n-d H:i:s')]);
        
        $form = DB::table('dx_forms')->where('list_id', '=', $file_field->list_id)->first();
        $request->merge(array(
            'edit_form_id' => $form->id,
            'item_id' => $down->item_id,
            'multi_list_id' => 0 // ToDo: implement multi list check
        ));
        
        $rez = $this->saveForm($request);
        
        return $rez;
    }
    
    public function saveFileGet(Request $request) {
                
        return response()->json([
            'success' => 1
        ]);
    }
    
    /**
     * Saglabā formas datus
     * 
     * @param \Illuminate\Http\Request $request POST pieprasījuma objekts
     * @return Response Saglabāšanas rezultāts JSON formātā
     * @throws Exceptions\DXCustomException
     */
    public function saveForm(Request $request)
    {
        $this->validate($request, [
            'edit_form_id' => 'required|integer|exists:dx_forms,id',
            'item_id' => 'integer',
            'multi_list_id' => 'integer'
        ]);

        $form_id = $request->input('edit_form_id');
        $item_id = $request->input('item_id');

        $this->checkSaveRights($form_id, $item_id);

        $save_obj = new FormSave($request);
        
        if ($save_obj->item_id > 0 && strlen($request->input("call_field_type", "")) > 0 && strlen($save_obj->call_field_type) == 0) {
            // Lookup field was not updated because it is updated in db by trigger
            // So, let's select updated value from db
            
            $fld = DB::table('dx_lists_fields')->where('id', '=', $request->input("call_field_id"))->first();
            $table = Workflows\Helper::getListTableName($fld->list_id);
            
            $data = DB::table($table)->select($fld->db_name . ' as txt')->where('id', '=', $save_obj->item_id)->first();
            $save_obj->call_field_htm_id = $request->input('call_field_htm_id');
            $save_obj->call_field_type = $request->input('call_field_type');
            $save_obj->call_field_id = $request->input('call_field_id');
            $save_obj->call_field_value = $data->txt;
        }
        
        return response()->json([
            'success' => 1, 
            'id' => $save_obj->item_id, 
            'gener_arr' => $save_obj->gener_arr, 
            'call_field_htm_id' => $save_obj->call_field_htm_id,
            'call_field_type' => $save_obj->call_field_type,
            'call_field_id' => $save_obj->call_field_id,
            'call_field_value' => $save_obj->call_field_value
        ]);
    }

    /**
     * Dzēš ierakstu no datu bāzes
     * Ja reģistram iespējota vēstures veidošana, tad pirms dzēšanas saglabā datus vēsturē
     * 
     * @param Request $request  POST pieprasījuma objekts
     * @return Response         JSON rezultāts par veiksmīgu dzēšanu
     */
    public function deleteItem(Request $request)
    {
        $this->validate($request, [
            'edit_form_id' => 'required|integer|exists:dx_forms,id',
            'item_id' => 'integer'
        ]);

        $form_id = $request->input('edit_form_id');
        $item_id = $request->input('item_id');

        $tbl = FormSave::getFormTable($form_id);

        checkDeleteRights($tbl->list_id);

        validateRelations($tbl->list_id, $item_id);
        
        $table_row = FormSave::getFormTable($form_id);
        $fields = FormSave::getFormsFields(-1, $form_id);
            
        DB::transaction(function () use ($table_row, $fields, $item_id)
        {
            \App\Libraries\Helper::deleteItem($table_row, $fields, $item_id);
        });        

        return response()->json(['success' => 1]);
    }

    /**
     * Nodrošina autocompleate lauka vērtību meklēšanu un attēlošanu pēc norādītās frāzes
     * 
     * @param \Illuminate\Http\Request $request AJAX POST pieprasījums
     * @return Response JSON ar atrastajām vērtībām
     */
    public function getAutocompleateData(Request $request)
    {
        $field_id = $request->input('field_id');
        $term = $request->input('q', Uuid::generate(4)); // We generate default value as GUID so ensure that nothing will be found if search criteria is not provided (temporary solution - as validation does not work)
        $list_id = $request->input('list_id');
        $txt_field_id = $request->input('txt_field_id');

        return response()->json(['success' => 1, 'data' => $this->getAutocompleateArray($list_id, $txt_field_id, $term, $field_id)]);
    }

    /**
     * Atgriež saistītās izkrītošās izvēlenes vērtības
     * 
     * @param integer $binded_field_id  Lauka ID, kuram piesaistīts saistītais lauks (abi ir izkrītošās izvēlnes)
     * @param integer $binded_rel_field_id Saistīā lauka ID
     * @param integer $binded_rel_field_value Ieraksta ID, pēc kura atlasīt saistītās vērtības
     * @return string   HTML ar ierakstiem attēlošanai SELECT elementā
     */
    public static function getBindedFieldsItems($binded_field_id, $binded_rel_field_id, $binded_rel_field_value)
    {
        $data = getBindedFieldsItems($binded_field_id, $binded_rel_field_id, $binded_rel_field_value);
        
        return view('elements.binded_items', ['data' => $data])->render();
    }

    /**
     * Uzstāda klases parametrus, kas nosaka formas rediģēšanas režīmu un tiesības
     * 
     * @param integer $list_id  Reģistra ID
     * @param integer $item_id  Ieraksta ID
     * @throws Exceptions\DXCustomException
     */
    protected function setFormsRightsMode($list_id, $item_id)
    {
        $this->list_right = Rights::getRightsOnList($list_id);
        
        if ($this->list_right == null) {
            
            if ($item_id == 0 || !Workflows\Helper::isRelatedTask($list_id, $item_id)) {
                throw new Exceptions\DXCustomException(trans('errors.no_rights_on_register'));
            }
            
            // var vismaz skatīties ieraksta kartiņu
            
            // Pārbauda vai var rediģēt
            if ($this->isRelatedEditableTask($list_id, $item_id)) {
                $this->is_disabled = 0; // var rediģēt ierakstu
            }
            else {
                $this->form_is_edit_mode = 0; // read only
            }
        }
        else {
            
            if ($item_id == 0) {
                if ($this->list_right->is_new_rights == 0) {           
                    throw new Exceptions\DXCustomException(trans('errors.no_rights_to_insert'));
                }
                $this->is_disabled = 0; // var rediģēt, pēc noklusēšanas ir ka nevar
            }
            else {
                if (Rights::isEditTaskRights($list_id, $item_id)) {
                    // Employee have task to edit this document
                    
                    $this->is_edit_rights = 1;
                    $this->is_delete_rights = 0; // because document is in workflow, so cant delete it
                }
                else {
                    
                    $is_item_editable_wf = Rights::getIsEditRightsOnItem($list_id, $item_id); // Check if not in workflow and not status finished

                    if (!$is_item_editable_wf) {
                        // Workflow is in process or finished - user does not have rights to edit/delete
                        $this->is_edit_rights = 0;
                        $this->is_delete_rights = 0;
                        $this->form_is_edit_mode = 0;
                    }
                    else {
                        // set rights acording to register role
                        
                        if ($this->list_right->is_only_own_rows == 0) {
                            $this->is_delete_rights = $this->list_right->is_delete_rights;
                            $this->is_edit_rights = $this->list_right->is_edit_rights;
                        }
                        else {
                            $table_name = \App\Libraries\DBHelper::getListObject($list_id)->db_name;
                            $fld_name = DB::table('dx_lists_fields')->where('id', '=', $this->list_right->is_only_own_rows)->first()->db_name;
                            $data_row = DB::table($table_name)
                                        ->select($fld_name . ' as user')
                                        ->where('id', '=', $item_id)
                                        ->first();
                            
                            if ($data_row->user == Auth::user()->id) {
                                $this->is_delete_rights = $this->list_right->is_delete_rights;
                                $this->is_edit_rights = $this->list_right->is_edit_rights;
                            }
                            else {
                                $this->is_edit_rights = 0;
                                $this->is_delete_rights = 0;
                                $this->form_is_edit_mode = 0;
                            }
                            
                        }
                    }
                }
                
            }            

            if ($this->is_edit_rights ) {
                $this->is_disabled = 0; // var rediģēt, pēc noklusēšanas ir ka nevar
            }
            
            if (!$this->is_edit_rights && !$this->list_right->is_new_rights) {
                $this->form_is_edit_mode = 0; // readonly
            }
        }
        
        $this->setFormEditMode($item_id);
    }       
    
     /**
     * Pārbauda vai lietotājam uz ierakstu ir izveidots kāds uzdevums, kas ir papildināt un saskaņot un ir procesā
     * Tādā gaījumā, ieraksts jāatver rediģēšanas režīmā
     * 
     * @param integer $list_id Reģista ID
     * @param integer $item_id Ieraksta ID
     * @return boolean Tru - ja ir kāds uzdevums, False - ja nav neviens uzdevums
     */
    private function isRelatedEditableTask($list_id, $item_id) {
        $task = DB::table('dx_tasks')
                ->where('list_id', '=', $list_id)
                ->where('item_id', '=', $item_id)
                ->where('task_employee_id', '=', Auth::user()->id)
                ->where('task_status_id', '=', TasksController::TASK_STATUS_PROCESS)
                ->where('task_type_id', '=', TasksController::TASK_TYPE_FILL_ACCEPT)
                ->first();
        
        return ($task != null);
    }

    /**
     * Uzstāda klases parametru vērtības atbilstoši tam, vai ir jauns ieraksts vai eksistējoša labošana
     * 
     * @param integer $item_id Ieraksta ID
     */
    private function setFormEditMode($item_id)
    {
        if ($item_id > 0) {
            if ($this->form_is_edit_mode == 0) {
                $this->is_disabled = 1; // read-only mode
            }
        }
        else {
            $this->form_is_edit_mode = 1;
        }
    }

    /**
     * Atgriež formas virsraksta izcēluma tekstu, atkarībā vai ir jauns ieraksts, rediģēšana vai skatīšanās režīms
     * 
     * @param integer $item_id           Ieraksta ID
     * @param boolean $form_is_edit_mode Pazīme, vai forma ir rediģēšanas režīmā ( 0 - nē, 1 - jā)
     * @return string   Formas virsraksta izcēluma teksts
     */
    private function getFormBadge($item_id, $form_is_edit_mode)
    {
        $form_badge = "";
        if ($item_id > 0) {
            $form_badge = ($form_is_edit_mode == 1) ? trans("form.badge_edit") : "";
        }
        else {
            $form_badge = trans("form.badge_new");
        }

        return $form_badge;
    }

    /**
     * Pārbauda, vai ir tiesības saglabāt formas datus 
     * 
     * @param integer $form_id  Formas ID
     * @param integer $item_id  Ieraksta ID
     * @throws Exceptions\DXCustomException
     */
    protected function checkSaveRights($form_id, $item_id)
    {
        $tbl = FormSave::getFormTable($form_id);

        Rights::checkListItemEditRights($tbl->list_id, $item_id);
    }

    /**
     * Uzstāda formas darbplūsmas objektu
     * 
     * @param integer $list_id Reģistra ID
     */
    private function setWorkflow($list_id) {
        $this->workflow = DB::table("dx_workflows_def")
                    ->where('list_id', '=', $list_id)
                    ->whereRaw('now() between ifnull(valid_from, DATE_ADD(now(), INTERVAL -1 DAY)) and ifnull(valid_to, DATE_ADD(now(), INTERVAL 1 DAY))')
                    ->first();
    }
    
    /**
     * Nosaka, vai ierakstam ir inicializēta darbplūsma un kāds ir tās statuss
     * Statuss tiks izmantots lai formā rādītu/nerādītu pogu, kas inicializē darbplūsmu
     * 
     * @param integer $list_id  Reģistra ID
     * @param integer $item_id  Ieraksta ID
     * @return int Darbplūsmas statuss ( 0 - nav definēta, 1 - ieraksts nav apstiprināts, 2 - darbplūsma ir procesā vai arī ieraksts ir apstiprināts)
     */
    private function isWorkflowInit($list_id, $item_id)
    {
        $cur_task = Workflows\Helper::getCurrentTask($list_id, $item_id);
            
        if ($cur_task) {
            $wf_info = DB::table('dx_workflows_info')
                        ->where('id','=',$cur_task->wf_info_id)
                        ->first();
            
            $this->is_wf_cancelable = ($wf_info->init_user_id == Auth::user()->id || ($this->list_right && $this->list_right->is_edit_rights && !$this->list_right->is_only_own_rows));
            return 2; // darbplūsma ir procesā
        }

        if ($this->workflow) {
            // Ir definēta darbplūsma
            
            $status = Workflows\Helper::getItemApprovalStatus($list_id, $item_id);
            
            if ($status == 3) { // item in status rejected
                $this->reject_task = Workflows\Helper::getWFRejectedInfo($list_id, $item_id);
            }
            
            return $status;
        }
        
        return 0; // reģistram nav definēta aktīva darbplūsma        
    }
   
    /**
     * Izgūst uzmeklēšanas lauka vērtību masīvu pēc norādītā meklēšanas kritērija
     * 
     * @param integer $list_id      Reģistra ID
     * @param integer $txt_field_id Teksta lauka ID
     * @param string  $term         Meklēšanas kritērija frāze
     * @return Array  Masīvs ar kritērijam atbilstošajām vērtībām  
     * @throws Exceptions\DXCustomException
     */
    private function getAutocompleateArray($list_id, $txt_field_id, $term, $field_id)
    {
        $table_item = DB::table('dx_lists')
                ->join('dx_objects', 'dx_lists.object_id', '=', 'dx_objects.id')
                ->select(DB::raw("dx_objects.db_name as table_name, dx_objects.is_multi_registers"))
                ->where('dx_lists.id', '=', $list_id)
                ->first();

        $field_item = DB::table('dx_lists_fields')
                ->select('db_name as rel_field_name', 'is_right_check')
                ->where('id', '=', $txt_field_id)
                ->first();
        
        $main_field_item = DB::table('dx_lists_fields')
                ->select('is_right_check')
                ->where('id', '=', $field_id)
                ->first();
        
        if (!$table_item || !$field_item || !$main_field_item) {
            throw new Exceptions\DXCustomException("Sistēmas konfigurācijas kļūda! Uzmeklēšanas laukam nav atrodams reģistrs ar ID " . $list_id . " vai saistītais lauks ar ID " . $txt_field_id . ".");
        }
        
        $field_item->is_right_check = $main_field_item->is_right_check; // jo tiesības uzstāda galvenā reģistra laukam bet SQLs tiek veidots no saistītā reģistra

        $rows = DB::select($this->getAutocompleateSQL($table_item, $field_item, $list_id), array($field_item->rel_field_name => "%" . $term . "%"));

        $rez = array();
        foreach ($rows as $item) {
            array_push($rez, array("id" => $item->id, "text" => $item->txt));
        }

        return $rez;
    }

    /**
     * Izveido SQL izteiksmi uzmeklēšanas lauka vērtību atlasei atbilstoši meklēšanas kritērijam
     * 
     * @param Object  $table_item   Tabulas objekts
     * @param Object $field_item    Lauka objekts
     * @param integer $list_id      Reģistra ID
     * @return string   SQL izteiksme
     */
    private function getAutocompleateSQL($table_item, $field_item, $list_id)
    {               
        $sql = getLookupSQL($list_id, $table_item->table_name, $field_item, "txt");
        
        $sql = $sql . " AND txt like :" . $field_item->rel_field_name . " ORDER BY txt ASC";
                
        return $sql;
    }

    /**
     * Izveido formas lauku HTML
     *
     * @param   string  $frm_uniq_id        Formas unikālais GUIDs
     * @param   int     $list_id            Reģistra ID
     * @param   int     $item_id            Ieraksta ID
     * @param   int     $parent_item_id     Saistītās formas ieraksta ID - ja forma atvērta no citas formas grida
     * @param   int     $parent_field_id    Saistītās formas lauka ID (pēc kura sasaistās abas formas)
     * @param   array   $params             Masīvs ar formas parametriem
     * @param   int     $is_disabled        Pazīme, vai forma jāattelo skatīšanās režīmā ( 0 - skatīšanās, 1 - rediģēšanas)
     * @return string Formas lauku HTML
     */
    protected function getFormFieldsHTML($frm_uniq_id, $list_id, $item_id, $parent_item_id, $parent_field_id, $params)
    {
        $row_data = null;

        if ($item_id > 0) {
            $row_data = $this->getFormItemDataRow($list_id, $item_id, $params);
        }

        $fields = $this->getFormFields($params);

        $fields_htm = "";

        $binded_field_id = 0;
        $binded_rel_field_id = 0;
        $binded_rel_field_value = 0;

        foreach ($fields as $row) {
            if ($row->db_name == "id" && $item_id == 0) {
                // skip ID field for new item form
                continue;
            }

            $fld_obj = new FormField($row, $list_id, $item_id, $parent_item_id, $parent_field_id, $row_data, $frm_uniq_id);
            $fld_obj->is_disabled_mode = $this->is_disabled;
            $fld_obj->is_item_editable = ($this->is_edit_rights && $this->is_editable_wf);
            
            $fld_obj->binded_field_id = $binded_field_id;
            $fld_obj->binded_rel_field_id = $binded_rel_field_id;
            $fld_obj->binded_rel_field_value = $binded_rel_field_value;

            $fld_obj->is_editable_wf = $this->is_editable_wf;
            
            if ($row->tab_id) {
                if (!isset($this->arr_data_tabs[$row->tab_id])) {
                    $this->arr_data_tabs[$row->tab_id] = "";
                }
                $this->arr_data_tabs[$row->tab_id] .= $fld_obj->get_field_htm();
            }
            else {
                $fields_htm .= $fld_obj->get_field_htm();
            }
            
            $binded_field_id = $fld_obj->binded_field_id;
            $binded_rel_field_id = $fld_obj->binded_rel_field_id;
            $binded_rel_field_value = $fld_obj->binded_rel_field_value;
        }

        return $fields_htm;
    }

    /**
     * Izgūst formas lauku masīvu
     *
     * @param  Array  $params Formas parametru masīvs
     * @return Object  Masīvs ar formas lauku objektiem
     */
    protected function getFormFields($params)
    {
        $fields = \App\Libraries\DBHelper::getFormFields($params->form_id);

        if (count($fields) == 0) {
            throw new Exceptions\DXCustomException("Forma ar ID " . $params->form_id . " nav atrasta!");
        }

        return $fields;
    }

    /**
     * Izgūst formā attēlojamā ieraksta lauku vērtības kā masīvu
     *
     * @param  integer  $list_id Reģistra ID
     * @param  integer  $item_id Ieraksta ID
     * @return Array  Masīvs ar ieraksta lauku vērtībām
     */
    protected function getFormItemDataRow($list_id, $item_id, $params)
    {
        $fields_rows = $this->getFormSQLFields($list_id);

        if (count($fields_rows) == 0) {
            throw new Exceptions\DXCustomException("Reģistrs ar ID " . $list_id . " nav atrasts!");
        }

        DB::setFetchMode(PDO::FETCH_ASSOC); // We need to get values dynamicly                   

        $rows = $this->getFormItemRows($fields_rows, $params, $item_id);

        DB::setFetchMode(PDO::FETCH_CLASS); // Set back default fetch mode

        if (count($rows) == 0) {
            throw new Exceptions\DXCustomException("Reģistra ar ID " . $list_id . " ieraksts ar ID " . $item_id . " nav atrasts!");
        }

        return $rows[0];
    }

    /**
     * Atgriež formas laukus
     * 
     * @param Object    $fields_rows   Masīvs ar formas laukiem
     * @param Object    $params        Formas parametri
     * @param integer   $item_id       Ieraksta ID
     * @return Array                   Masīvs ar formas laukiem
     */
    private function getFormItemRows($fields_rows, $params, $item_id)
    {        
        $arr_flds = array();
        
        foreach ($fields_rows as $row) {

            if ($row->sys_name == "datetime") {                
                array_push($arr_flds, DB::raw("DATE_FORMAT(" . $row->db_name . ",'%d.%m.%Y %H:%i') as " . $row->db_name));
            }
            else if ($row->sys_name == "date") {
                array_push($arr_flds, DB::raw("DATE_FORMAT(" . $row->db_name . ",'%d.%m.%Y') as " . $row->db_name));
            }
            else if ($row->sys_name == "file") {
                array_push($arr_flds, $row->db_name);
                array_push($arr_flds, str_replace("_name", "_guid", $row->db_name));
            }
            else {
                array_push($arr_flds, $row->db_name);
            }
        }

        return DB::table($params->list_obj_db_name)
               ->select($arr_flds)
               ->where('id', '=', $item_id)
               ->get();
    }

    /**
     * Izgūst formas lauku masīvu - tiks izmantots, lai izveidotu SQL izteiksmi formas lauku vērtību atlasei
     *
     * @param  integer  $list_id Reģistra ID
     * @return Object Formas lauku masīvs
     */
    private function getFormSQLFields($list_id)
    {
        $sql = "
        SELECT
                lf.db_name,
                lf.title_list,
                ft.is_date,
                ft.is_integer,
                ft.is_decimal,
                o.db_name as rel_table_db_name,
                rf.db_name as rel_field_db_name,
                ft.sys_name	
        FROM
                dx_lists_fields lf
                inner join dx_field_types ft on lf.type_id = ft.id
                left join dx_lists_fields rf on lf.rel_display_field_id = rf.id
                left join dx_lists rl on rl.id = lf.rel_list_id
                left join dx_objects o on rl.object_id = o.id
        WHERE
                lf.list_id = :list_id and (lf.formula is null or lf.rel_list_id is not null)
        ";

        return DB::select($sql, array("list_id" => $list_id));
    }

    /**
     * Izgūst formas parametrus
     *
     * @param  integer  $list_id Reģistra ID
     * @return Object Formas parametri
     */
    protected function getFormParams($list_id)
    {
        $sql = "
	SELECT
		o.db_name as list_obj_db_name,
		f.id as form_id,
		f.title as form_title,
		f.zones_count,
		o.is_multi_registers,
		f.width,
                f.is_vertical_tabs,
                f.is_full_screen_mode
	FROM
		dx_lists l
		inner join dx_objects o on l.object_id = o.id
		inner join dx_forms f on f.list_id = l.id
	WHERE
		     l.id = :list_id
	LIMIT 0,1
	";

        $list_rows = DB::select($sql, array("list_id" => $list_id));

        if (count($list_rows) == 0) {
            throw new Exceptions\DXListNotFoundException($list_id);
        }

        return $list_rows[0];
    }

    /**
     * Izgūst formas sadaļu masīvu atbilstoši lietotāja tiesībām uz sadaļās iekļautajiem reģistriem
     *
     * @param  int  $form_id Formas ID
     * @return Object Masīvs ar formas sadaļām
     */
    protected function getFormTabs($form_id)
    {
        $sql = "
        SELECT 
            * 
        FROM 
            dx_forms_tabs 
        WHERE 
            form_id = :form_id 
            AND (
                    grid_list_id is null 
                OR  grid_list_id in 
                    (
                    select distinct 
                        rl.list_id 
                    from 
                        dx_users_roles ur 
                        inner join dx_roles_lists rl on ur.role_id = rl.role_id 
                    where 
                        ur.user_id = :user_id
                    )
                ) 
        ORDER BY 
            order_index
        ";

        return DB::select($sql, array("form_id" => $form_id, 'user_id' => Auth::user()->id));
    }

    /**
     * Izgūst formas sadaļu HTML
     *
     * @param   string  $tab_id         Sadaļu ietvara unikālais GUID
     * @param   object     $params      Formas parametri
     * @param   string  $frm_uniq_id    Formas unikālais GUID
     * @return  int     $item_id        Ieraksta ID
     */
    private function getFormTabsHTML($tab_id, $params, $frm_uniq_id, $item_id)
    {
        $tabs_items = $this->getFormTabs($params->form_id);
        $tabs_htm = "";

        if (count($tabs_items) > 0) {

            foreach($tabs_items as $tab) {
                $tab->data_htm = "";
                if ($tab->is_custom_data && isset($this->arr_data_tabs[$tab->id])) {
                    $tab->data_htm = $this->arr_data_tabs[$tab->id];
                }
            }
            
            $view_type = ($params->is_vertical_tabs) ? "elements.tabs_vert" : "elements.tabs";
            
            $tabs_htm = view($view_type, [
                'tab_id' => $tab_id,
                'tabs_items' => $tabs_items,
                'frm_uniq_id' => $frm_uniq_id,
                'item_id' => $item_id
                    ])->render();
        }

        return $tabs_htm;
    }

}

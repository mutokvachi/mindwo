<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use PDO;
use Auth;
use Webpatser\Uuid\Uuid;
use App\Exceptions;
use Config;
use App\Jobs\SendTaskEmail;
use App\Libraries\Rights;
use App\Libraries\Workflows;
use Log;

/**
 * Klase nodrošina darbplūsmu un uzdevumu funkcionalitāti
 * Nākotnē darbplūsmu definēšanai tiks izmantots šī grafiskās vizualizācijas komponente: http://gojs.net/latest/samples/customContextMenu.html (grafiskais redaktors)
 */
class TasksController extends Controller
{   
    /**
     * Darbinieks (no tabulas dx_tasks_perform)
     */
    const PERFORM_EMPLOYEE = 1;
    
    /**
     * Tiešais vadītājs (no tabulas dx_tasks_perform)
     */
    const PERFORM_MANAGER = 2;
    
    /**
     * Dokumenta izveidotājs (no tabulas dx_tasks_perform)
     */
    const PERFORM_CREATOR = 3;
    
    /**
     * Reģistrācijas numurs (no tabulas dx_field_represent)
     */
    const REPRESENT_REG_NR = 6;
    
    /**
     * Dokumenta saturs (no tabulas dx_field_represent)
     */
    const REPRESENT_ABOUT = 7;
    
    /**
     * Employee to which document applays
     */
    const REPRESENT_EMPL = 9;
    
    /**
     * Employee which is responsible about document
     */
    const REPRESENT_RESPONSIBLE_EMPL = 10;
    
    /**
     * Uzdevuma veids - saskaņot
     */
    const TASK_TYPE_APPROVE = 1;
    
    /**
     * Uzdevuma veids - papildināt un saskaņot (no tabulas dx_tasks_types)
     */
    const TASK_TYPE_FILL_ACCEPT = 3;

    /**
     * Uzdevuma veids - vērtības uzstādīšana (no tabulas dx_tasks_types)
     */
    const TASK_TYPE_SET_VAL = 4;
    
    /**
     * Uzdevuma veids - kritērijs (no tabulas dx_tasks_types)
     */
    const TASK_TYPE_CRITERIA = 5;
    
    /**
     * Uzdevuma veids - informācijai (no tabulas dx_tasks_types)
     */
    const TASK_TYPE_INFO = 6;
    
    /**
     * Uzdevuma veids - kritērijs, kas nosaka, vai ir iestatīta manuālā saskaņošana
     */
    const TASK_TYPE_WF_CRITERIA = 7;

    /**
     * Uzdevuma statuss - procesā (no tabulas dx_tasks_statuses)
     */
    const TASK_STATUS_PROCESS = 1;
    
    /**
     * Uzdevuma statuss - izpildīts (no tabulas dx_tasks_statuses)
     */
    const TASK_STATUS_DONE = 2;

    /**
     * Uzdevuma statuss - noraidīts (no tabulas dx_tasks_statuses)
     */
    const TASK_STATUS_DENY = 3;
    
    /**
     * Uzdevuma statuss - anulēts (no tabulas dx_tasks_statuses)
     */
    const TASK_STATUS_CANCEL = 4;
    
     /**
     * Uzdevuma statuss - deleģēts
     */
    const TASK_STATUS_DELEGATE = 5;
    
    /**
     * Reģistra lauka tips - teksts (no tabulas dx_field_types)
     */
    const FIELD_TYPE_TEXT = 1;
    
    /**
     * Reģistra lauka tips - garš teksts (no tabulas dx_field_types)
     */
    const FIELD_TYPE_LONG_TEXT = 4;
    
    /**
     * Reģistra lauka tips - skaitlis (no tabulas dx_field_types)
     */
    const FIELD_TYPE_INT = 5;

    /**
     * Reģistra lauka tips - uzmeklēšanas ieraksts (no tabulas dx_field_types)
     */
    const FIELD_TYPE_LOOKUP = 8;
    
    /**
     * Reģistra lauka tips - datums (no tabulas dx_field_types)
     */
    const FIELD_TYPE_DATE = 9;

    /**
     * Darbplūsmas statuss - apstiprināts (no tabulas dx_item_statuses)
     */
    const WORKFLOW_STATUS_APPROVED = 4;

    /**
     * Reģistram definētās aktīvās darbplūsmas ID
     * 
     * @var integer 
     */
    private $workflow_id = 0;
    
    /**
     * Darbplūsmas startēšanas informācijas ieraksta ID
     * 
     * @var integer 
     */
    private $wf_info_id = 0;
    
    /**
     * Jaunizveidotā uzdevuma ID
     * 
     * @var integer 
     */
    private $new_task_id = 0;
    
    /**
     * Aktuālā darbplūsma, kas ir definēta reģistram
     * @var object
     */
    private $workflow = null;
    
    /**
     * If item rejected - rejection info data
     * @var object 
     */
    //public $reject_task = null;
    
    /**
     * Cancels workflow execution process
     * 
     * @param \Illuminate\Http\Request $request
     * @return type
     * @throws Exceptions\DXCustomException
     */
    public function cancelWorkflow(Request $request) {
        $this->validate($request, [
            'item_id' => 'required|integer',
            'list_id' => 'required|integer|exists:dx_lists,id',
            'comment' => 'required'
        ]);
        
        $list_id = $request->input('list_id');
        $item_id = $request->input('item_id');
        $comment = $request->input('comment');
        
        $cur_task = Workflows\Helper::getCurrentTask($list_id, $item_id);
        
        if (!$cur_task) {
            throw new Exceptions\DXCustomException(trans('task_form.err_wf_not_in_process'));
        }
        
        $list_right = Rights::getRightsOnList($list_id);
        
        $wf_info = DB::table('dx_workflows_info')
                    ->where('id','=',$cur_task->wf_info_id)
                    ->first();

        $is_wf_cancelable = ($wf_info->init_user_id == Auth::user()->id || ($list_right && $list_right->is_edit_rights && !$list_right->is_only_own_rows));
        
        if (!$is_wf_cancelable) {
            throw new Exceptions\DXCustomException(trans('task_form.err_wf_no_rights_to_cancel'));
        }
        
        $item_table = \App\Libraries\DBHelper::getListObject($list_id)->db_name;
        
        DB::transaction(function () use ($item_id, $list_id, $wf_info, $item_table, $cur_task, $comment) {
            // close all inprocess tasks
            DB::table("dx_tasks")
                ->where('item_id', '=', $item_id)
                ->where('list_id', '=', $list_id)
                ->whereNull('task_closed_time')
                ->where('task_type_id', '!=', self::TASK_TYPE_INFO)
                ->update([
                'task_closed_time' => date("Y-m-d H:i:s"), 
                'task_status_id' => self::TASK_STATUS_CANCEL, 
                'task_comment' => sprintf(trans('task_form.comment_anulated'), Auth::user()->display_name) . ' ' . $comment
            ]);
            
            // set wf info about cancelation
            DB::table('dx_workflows_info')->where('id', '=', $wf_info->id)->update([
                'end_time' => date("Y-m-d H:i:s"),
                'end_user_id' => Auth::user()->id,
                'is_forced_end' => 1,
                'comment' => $comment
            ]);
            
            // reject list item
            DB::table($item_table)->where('id', '=', $item_id)->update(['dx_item_status_id' => 3]);
            
        });
        
        return response()->json([
            'success' => 1,
            'left_btns' => $this->getFormTopMenuLeft($list_id, $item_id, 3)
        ]);
    }
    
    /**
     * Returns workflow tasks history information
     * 
     * @param \Illuminate\Http\Request $request AJAX request
     * @return Response JSON result
     */
    public function getTasksHistory(Request $request) {
        $this->validate($request, [
            'item_id' => 'required|integer',
            'list_id' => 'required|integer|exists:dx_lists,id'            
        ]);
        
        $list_id = $request->input('list_id');
        $item_id = $request->input('item_id');
                
        $right = Rights::getRightsOnList($list_id);

        if ($right == null) {
            if (!\App\Libraries\Workflows\Helper::isRelatedTask($list_id, $item_id)) {
                throw new Exceptions\DXCustomException(trans('task_form.err_no_list_rights'));
            }
        }            
        /*
        $status = Workflows\Helper::getItemApprovalStatus($list_id, $item_id);            
        if ($status == 3) { // item in status rejected
            $this->reject_task = Workflows\Helper::getWFRejectedInfo($list_id, $item_id);
        }
        */
        $tasks_view = \App\Libraries\Blocks\TaskListViews\TaskListViewFactory::build_taskview("DOC_HISTORY");
        $tasks_view->list_id = $list_id;
        $tasks_view->item_id = $item_id;
        
        $html = view('workflow.task_history', [
                'tasks' => $tasks_view->getRows(),
                'profile_url' => Config::get('dx.employee_profile_page_url'),
                'self' => $this
                ])->render();
        
        return response()->json(['success' => 1, 'html' => $html]);
    }
    
    /**
     * Returns workflow initiator info by wf instance ID
     * 
     * @param integer $wf_init_id Workflow instance ID
     * @return object Workflow init info
     */
    public function getWfInitInfo($wf_init_id) {
        return DB::table('dx_workflows_info as wf')
               ->select(
                    'u.display_name',
                    'u.picture_guid',
                    'wf.init_user_id',
                    'wf.init_time'                           
               )
               ->leftJoin('dx_users as u', 'wf.init_user_id', '=', 'u.id')
               ->where('wf.id', '=', $wf_init_id)
               ->first();
    }
    
    /**
     * Saglabā deleģēto uzdevumu
     * 
     * @param \Illuminate\Http\Request $request AJAX POST pieprasījums
     * @return Response JSON rezultāts
     */
    public function saveDelegateTask(Request $request) {
        $this->validate($request, [
            'task_id' => 'required|integer|exists:dx_tasks,id',
            'due_date' => 'required',
            'task_txt' => 'required',
            'employee_id' => 'required|integer|exists:dx_users,id'
        ]);
        
        $due_date = check_date($request->input('due_date'), Config::get('dx.date_format'));
            
        if (strlen($due_date) == 0)
        {
            throw new Exceptions\DXCustomException(sprintf(trans('task_form.err_date_format'), Config::get('dx.date_format')));
        }
        
        $task_row = $this->getTaskRow($request->input('task_id'),  Auth::user()->id);
                
        if (strtotime($task_row->due_date) < strtotime($due_date)) {
            throw new Exceptions\DXCustomException(sprintf(trans('task_form.err_date_delegate'), short_date($task_row->due_date)));
        }
        
        $employee_id = $request->input('employee_id');
        
        // pārbauda prombūtni
        $subst_empl = \App\Libraries\Workflows\Helper::getSubstitEmpl($employee_id, "");
        $subst_info = null;
        
        if ($employee_id != $subst_empl['employee_id']) {
            
            if ($subst_empl['employee_id'] == Auth::user()->id) {
                throw new Exceptions\DXCustomException(sprintf(trans('task_form.err_subst_delegate'), $subst_empl['subst_info']));
            }
            // todo: iespējams jāpiedāvā izvēlēties..
            // pagaidām deleģējam aizvietotājam
            $employee_id = $subst_empl['employee_id'];
            $subst_info = $subst_empl['subst_info'];
            
            $assigner = Auth::user()->display_name;
        }
        else {
            $assigner = $subst_empl['display_name'];
        }
        
        DB::transaction(function () use ($task_row, $request, $employee_id, $subst_info, $due_date) {
            $this->new_task_id = DB::table('dx_tasks')->insertGetId([
                'assigned_empl_id' => Auth::user()->id,
                'parent_task_id' => $task_row->id,
                'due_date' => $due_date,
                'task_details' => $request->input('task_txt'),
                'created_user_id' => Auth::user()->id,
                'created_time' => date('Y-n-d H:i:s'),
                'modified_user_id' => Auth::user()->id,
                'modified_time' => date('Y-n-d H:i:s'),
                'list_id' => $task_row->list_id,
                'item_id' => $task_row->item_id,
                'item_reg_nr' => $task_row->item_reg_nr,
                'item_info' => $task_row->item_info,
                'task_type_id' => $task_row->task_type_id,
                'task_created_time' => date('Y-n-d H:i:s'),
                'task_status_id' => self::TASK_STATUS_PROCESS,
                'task_employee_id' => $employee_id,
                'step_id' => $task_row->step_id,
                'substit_info' => $subst_info,
                'wf_info_id' => $this->wf_info_id
            ]);
            
            DB::table('dx_tasks')->where('id', '=', $task_row->id)->update(['task_status_id' => self::TASK_STATUS_DELEGATE]);
        });
        
        $this->sendNewTaskEmail([
            'email' => $subst_empl['email'],
            'subject' => 'Jauns uzdevums sistēmā MEDUS',
            'task_type' => DB::table('dx_tasks_types')->select('title')->where('id', '=', $task_row->task_type_id)->first()->title,
            'task_details' => $request->input('task_txt'),
            'assigner' => $assigner,
            'due_date' => $due_date,
            'list_title' => DB::table('dx_lists')->select('list_title')->where('id', '=', $task_row->list_id)->first()->list_title,
            'doc_id' => $task_row->item_id,
            'doc_about' => $task_row->item_info,
            'task_id' => $this->new_task_id,
            'date_now' => date('Y-n-d H:i:s')
        ]);
        
        return response()->json(['success' => 1, 'status' => trans('task_form.status_delegated')]);
    }
    
    /**
     * Izveido informatīvo uzdevumu
     * 
     * @param \Illuminate\Http\Request $request
     * @return type
     */
    public function sendInfoTask(Request $request) {
        $this->validate($request, [
            'list_id' => 'required|integer|exists:dx_lists,id',
            'item_id' => 'required|integer',
            'empl_id' => 'required|integer|exists:dx_users,id'
        ]);
        
        $item_id = $request->input('item_id');
        $list_id = $request->input('list_id');
        $employee_id = $request->input('empl_id');
        
        if ($employee_id == Auth::user()->id) {
            throw new Exceptions\DXCustomException(trans('task_form.err_rights_exists'));
        }
        
        $right = Rights::getRightsOnList($list_id);

        if ($right == null) {
            if (!\App\Libraries\Workflows\Helper::isRelatedTask($list_id, $item_id)) {
                throw new Exceptions\DXCustomException(trans('task_form.err_no_list_rights'));
            }
        }        
        
        $task = DB::table('dx_tasks')
                ->where('list_id', '=', $list_id)
                ->where('item_id', '=', $item_id)
                ->where('task_employee_id', '=', $employee_id)
                ->where('task_type_id', '=', self::TASK_TYPE_INFO)
                ->first();
        
        if ($task) {
            throw new Exceptions\DXCustomException(trans('task_form.err_allready_informed'));
        }
        
        $list_table = \App\Libraries\Workflows\Helper::getListTableName($list_id);
        
        $arr_meta_vals = \App\Libraries\Workflows\Helper::getMetaFieldVal($list_table, $list_id, $item_id);
        
        $reg_nr = $arr_meta_vals[self::REPRESENT_REG_NR];
        $info = $arr_meta_vals[self::REPRESENT_ABOUT];
        $item_empl_id = $arr_meta_vals[self::REPRESENT_EMPL];
        
        DB::transaction(function () use ($request, $employee_id, $list_id, $item_id, $reg_nr, $info, $item_empl_id) {
            $this->new_task_id = DB::table('dx_tasks')->insertGetId([
                'assigned_empl_id' => Auth::user()->id,
                'task_details' => $request->input('task_info'),
                'created_user_id' => Auth::user()->id,
                'created_time' => date('Y-n-d H:i:s'),
                'modified_user_id' => Auth::user()->id,
                'modified_time' => date('Y-n-d H:i:s'),
                'list_id' => $list_id,
                'item_id' => $item_id,
                'item_reg_nr' => $reg_nr,
                'item_info' => $info,
                'task_type_id' => self::TASK_TYPE_INFO,
                'task_created_time' => date('Y-n-d H:i:s'),
                'task_status_id' => self::TASK_STATUS_PROCESS,
                'task_employee_id' => $employee_id,
                'item_empl_id' => $item_empl_id
            ]);
        });
        
        $email = DB::table('dx_users')->where('id', '=', $employee_id)->first()->email;
        
        if ($email) {
            $this->sendNewTaskEmail([
                'email' => $email,
                'subject' => sprintf(trans('task_email.subject'), trans('index.app_name')),
                'task_type' => DB::table('dx_tasks_types')->select('title')->where('id', '=', self::TASK_TYPE_INFO)->first()->title,
                'task_details' => $request->input('task_info'),
                'assigner' => Auth::user()->display_name,
                'due_date' => null,
                'list_title' => DB::table('dx_lists')->select('list_title')->where('id', '=', $list_id)->first()->list_title,
                'doc_id' => $item_id,
                'doc_about' => $info,
                'task_id' => $this->new_task_id,
                'date_now' => date('Y-n-d H:i:s')
            ]);
        }
        
        return response()->json(['success' => 1]);
    }
    
    /**
     * Izsūta e-pasta notifikāciju par jaunu uzdevumu
     * 
     * @param array $arr_data Masīvs ar e-pasta datiem
     */
    private function sendNewTaskEmail($arr_data) {
        $this->dispatch(new SendTaskEmail($arr_data));
    }
    
    /**
     * Atgriež uzdevuma formas HTML
     * 
     * @param \Illuminate\Http\Request $request
     * @return Response JSON dati ar formas HTML
     */
    public function getTaskForm(Request $request)
    { 
        $this->validate($request, [
            'item_id' => 'required|integer|exists:dx_tasks,id'
        ]);
        
        // Must have parameters for form loading
        $item_id = $request->input('item_id');
        
        $grid_htm_id = $request->input('grid_htm_id', '');
        
        $frm_uniq_id = Uuid::generate(4);
        $is_disabled = 1; //read-only rights by default
        
           
        $raw_sql = "
            t.id,
            t.item_id,
            t.list_id,
            t.item_reg_nr,
            t.item_info,
            t.task_created_time,
            t.task_comment,
            t.task_closed_time,
            t.task_employee_id as employee_id,
            u.display_name as employee_name,
            l.list_title as register_name,
            ts.title as status_title,
            t.task_details,
            t.task_type_id,
            tt.title as task_type_title,
            t.due_date,
            u_del.display_name as task_creator_name,
            t.substit_info
        ";

        $task_row = DB::table('dx_tasks as t')
                    ->select(DB::raw($raw_sql))
                    ->join('dx_tasks_types as tt', 't.task_type_id', '=', 'tt.id')
                    ->join('dx_tasks_statuses as ts', 't.task_status_id', '=', 'ts.id')
                    ->join('dx_lists as l', 't.list_id', '=', 'l.id')
                    ->join('dx_users as u', 't.task_employee_id', '=', 'u.id')
                    ->leftJoin('dx_users as u_del', 't.assigned_empl_id', '=', 'u_del.id')
                    ->where('t.id','=', $item_id)->first();

        if (!$task_row->task_closed_time && $task_row->employee_id == Auth::user()->id)
        {
            $is_disabled = 0;
        }

        $form_url = getListFormURL($task_row->list_id);
        $frm_uniq_id_js = str_replace("-", "_", $frm_uniq_id);

        $form_htm = view('workflow.task_form', [
                'frm_uniq_id' => $frm_uniq_id, 
                'form_title' => trans('task_form.form_title'),
                'task_row' => $task_row,
                'is_disabled' => $is_disabled,                    
                'grid_htm_id' => $grid_htm_id,
                'item_id' => $item_id,
                'form_url' => $form_url,
                'rel_list_id' => $task_row->list_id,
                'frm_uniq_id_js' => $frm_uniq_id_js,
                'rel_field_id' => 0,
                'employees' => DB::table('dx_users')->where('manager_id', '=', Auth::user()->id)->whereNotNull('login_name')->whereNull('valid_to')->orderBy('display_name', 'ASC')->get(),
                'date_format' => Config::get('dx.txt_date_format', 'd.m.Y')
                ])->render();

        return response()->json(['success' => 1, 'html' => $form_htm]);

    }
    
    /**
     * Izpilda uzdevumu (pozitīvs lēmums)
     * 
     * @param \Illuminate\Http\Request $request POST pieprasījuma objekts
     * @return Response JSON ar uzdevuma izpildes statusu
     */
    public function doYes(Request $request)
    {
        $this->validate($request, [
            'item_id' => 'required|integer|exists:dx_tasks,id'
        ]);
        
        $item_id = $request->input('item_id');
        $comment = $request->input('task_comment');
        
        $this->performTask($item_id, 1, $comment);
        
        return response()->json(['success' => 1, 'task_status' => trans('task_form.status_done'), 'tasks_count' => getUserActualTaskCount()]);
    }
    
     /**
     * Noraida uzdevumu (negatīvs lēmums)
     * 
     * @param \Illuminate\Http\Request $request POST pieprasījuma objekts
     * @return Response JSON ar uzdevuma izpildes statusu
     */
    public function doNo(Request $request)
    {
        $this->validate($request, [
            'item_id' => 'required|integer|exists:dx_tasks,id'
        ]);
        
        $item_id = $request->input('item_id');
        $comment = $request->input('task_comment');
        
        if (strlen($comment) == 0) {
            throw new Exceptions\DXCustomException(trans('task_form.err_comment_required'));
        }
        
        $this->performTask($item_id, 0, $comment);
        
        return response()->json(['success' => 1, 'task_status' => trans('task_form.status_rejected'), 'tasks_count' => getUserActualTaskCount()]);
        
    }
    
    /**
     * Atgriež JSON ar darbplūsmas noklusētajiem saskaņotājiem, kas tiek izgūti no darbplūsmas soļiem ar veidu "Saskaņot"
     * 
     * @param \Illuminate\Http\Request $request POST pieprasījuma objekts
     * @return Response JSON ar saskaņotāju HTML
     */
    public function getCustomApprove(Request $request) {
        $this->validate($request, [
            'list_id' => 'required|integer|exists:dx_lists,id',
            'item_id' => 'required|integer'
        ]);
        
        $list_id = $request->input('list_id');
        $item_id = $request->input('item_id');
        
        $this->setActiveWorkflow($list_id);
        
        if (!$this->workflow->is_custom_approve) {
            throw new Exceptions\DXCustomException(trans('workflow.wf_init_err_not_enabled'));
        }
        
        $approval_steps = $this->getWorkflowStepTable($list_id, 0)                
                                ->where('wf.task_type_id', '=', self::TASK_TYPE_APPROVE)
                                ->where('tp.code', '!=', 'CUSTOM_APPROVERS')
                                ->orderBy('wf.step_nr')
                                ->get();
        
        $arr_approvers = array();
        $arr_ids = array();
        array_push($arr_ids, 0);
        
        foreach($approval_steps as $step) {
            $performer_obj = \App\Libraries\Workflows\Performers\PerformerFactory::build_performer($step, $item_id, 0);
            $performers = $performer_obj->getEmployees();
            
            foreach($performers as $performer) {
                
                if (!array_search($performer["empl_id"], $arr_ids)) {
                    array_push($arr_approvers, [
                        'employee_id' => $performer["empl_id"],
                        'display_name' => $performer["subst_data"]["display_name"],
                        'subst_info' => $performer["subst_data"]["subst_info"],
                        'due_days' => $performer["due_days"],
                        'step_title' => $step->step_title,
                        'picture_guid' => $performer["subst_data"]['picture_guid'],
                        'position_title' => $performer["subst_data"]['position_title'],
                    ]);
                    array_push($arr_ids, $performer["empl_id"]);
                }
            }
        }
        
        $html = view('workflow.wf_init_approvers', [
                     'approvers' => $arr_approvers
        ])->render();
                
        return response()->json(['success' => 1, 'html' => $html]);
    }
    
     /**
     * Nodrošina autocompleate lauka darbinieku (saskaņotāju) meklēšanu un attēlošanu pēc norādītās frāzes
     * 
     * @param \Illuminate\Http\Request $request AJAX POST pieprasījums
     * @return Response JSON ar atrastajiem darbiniekiem
     */
    public function getAutocompleateApprovers(Request $request)
    {
        $term = $request->input('q', Uuid::generate(4)); // We generate default value as GUID so ensure that nothing will be found if search criteria is not provided (temporary solution - as validation does not work)
        $term = "%" . $term . "%";
        
        DB::setFetchMode(PDO::FETCH_ASSOC);
        
        $employees = DB::table('dx_users')
                    ->select('id', 'display_name as text', 'position_title')
                     ->where('display_name', 'like', $term)
                     ->whereNotNull('login_name')
                     ->whereNull('valid_to')
                     ->orderBy('display_name', 'ASC')
                     ->get();
        
        DB::setFetchMode(PDO::FETCH_CLASS);
        
        return response()->json(['success' => 1, 'data' => $employees]);
    }
    
    /**
     * Inicializē darbplūsmu
     * 
     * @param \Illuminate\Http\Request $request POST pieprasījuma objekts
     * @return Response JSON rezultāts ar dokumenta statusu
     */
    public function initWorkflow(Request $request)
    {
        $this->validate($request, [
            'list_id' => 'required|integer|exists:dx_lists,id',
            'item_id' => 'required|integer'
        ]);
                
        $list_id = $request->input('list_id');
        $item_id = $request->input('item_id');
        
        $this->checkRights($list_id);
       
        $this->setActiveWorkflow($list_id);
        
        $first_step_nr = $this->getFirstStepNr($list_id);
        
        DB::transaction(function () use ($list_id, $item_id, $first_step_nr, $request) {
            
            $is_paralel = $request->input('is_paralel', 0);
            
            $this->wf_info_id = DB::table('dx_workflows_info')
                                ->insertGetId([
                                    'init_user_id' => Auth::user()->id,
                                    'workflow_def_id' => $this->workflow_id,
                                    'init_time' => date("Y-m-d H:i:s"),
                                    'is_paralel_approve' => $is_paralel
                                ]);
            
            $custom_approvers = $request->input('approvers', '');
            
            if (strlen($custom_approvers) > 0) {
                $this->saveCustomApprovers($custom_approvers);
            }
            
            // we start again with the 1st step (can be n paralel)
            $steps = $this->getNextStep($first_step_nr, $list_id, $item_id, 1); // get next closest acceptance task (or n tasks in paralel)

            if (count($steps) > 0)
            {
                $this->newAcceptanceTaskDocInfo($steps, $item_id);
            }

        });
        
        // Current doc status, send as response to update interface
        return response()->json([
            'success' => 1, 
            'doc_status' => trans('task_form.doc_in_process'),
            'status_btn' => view('workflow.wf_status_btn',[
                                'workflow_btn' => 2, // in process
                                'is_wf_cancelable' => 1
                            ])->render(),
            'left_btns' => $this->getFormTopMenuLeft($list_id, $item_id, 2)
        ]); 
       
    }
    
    /**
     * Gets HTML for forms top menu left side buttons
     * 
     * @param integer $list_id List ID
     * @param integer $item_id Item ID
     * @param integer $workflow_btn Workflow button showing status (2 - in process/hide, other - show)
     * @return string HTML for top menu left side buttons
     */
    private function getFormTopMenuLeft($list_id, $item_id, $workflow_btn) {
        $table_name = \App\Libraries\DBHelper::getListObject($list_id)->db_name;
        
        return  view('elements.form_left_btns', [
                                'is_edit_rights' => 1, // because was able to start workflow
                                'is_delete_rights' => 1, // because was able to start workflow
                                'form_is_edit_mode' => 0,
                                'workflow_btn' => $workflow_btn,
                                'item_id' => $item_id,
                                'is_editable_wf' => Rights::getIsEditRightsOnItem($list_id, $item_id),
                                'is_info_tasks_rights' => ($table_name == "dx_doc"),
                                'is_word_generation_btn' => \App\Libraries\Helper::getWordGenerBtn($list_id),
                                'info_tasks' => \App\Libraries\Helper::getInfoTasks($list_id, $item_id, $table_name),
                                'list_id' => $list_id
                ])->render();
    }
    
    /**
     * Saglabā speciālos sakaņotājus - ja manuāla saksaņošanas iestatīšana bijusi
     * @param string $custom_approvers Teksts ar saskaņotājiem JSON formātā
     */
    private function saveCustomApprovers($custom_approvers) {
        $json_data = json_decode($custom_approvers);
        $idx = 0;
        foreach($json_data as $item){
            $idx++;
            DB::table('dx_workflows_approve')->insert([
                'workflow_info_id' => $this->wf_info_id,
                'approver_id' => $item->empl_id,
                'due_days' => $item->due_days,
                'order_index' => $idx*10
            ]);
        }
    }
    
    /**
     * Pārbauda tiesības uz reģistru - darbplūsmu var uzstākt tikai lietotājs ar rediģēšanas tiesībām
     * 
     * @param integer $list_id Reģistra ID
     * @throws Exceptions\DXCustomException
     */
    private function checkRights($list_id) {
        $right = \mindwo\pages\Rights::getRightsOnList($list_id);

        if ($right == null || !$right->is_edit_rights) {
            throw new Exceptions\DXCustomException(trans('task_form.err_no_list_rights'));
        }
    }
    
    /**
     * Atrod un uzstāda reģistra aktuālo darbplūsmu
     * 
     * @param integer $list_id Reģistra ID
     * @throws Exceptions\DXCustomException
     */
    private function setActiveWorkflow($list_id) {
        $this->workflow = DB::table('dx_workflows_def')
                    ->where('list_id', '=', $list_id)
                    ->where('valid_from', '<=', date('Y-n-d'))
                           ->where(function($query) {
                               $query->whereNull('valid_to')
                                     ->orWhere('valid_to', '>=', date('Y-n-d'));
                           })
                    ->first();
                    
        if (!$this->workflow) {
            throw new Exceptions\DXCustomException(trans('task_form.err_no_workflow'));            
        }
        
        $this->workflow_id = $this->workflow->id;
    }
    
    /**
     * Atgriež darbplūsmas pirmā soļa numuru
     * 
     * @param integer $list_id Reģistra ID
     * 
     * @return integer Pirmā soļa numurs
     * @throws Exceptions\DXCustomException
     */
    private function getFirstStepNr($list_id) {
        
        // Here we get min step number - this will be used to start 1st step
        $first_step = $this->getWorkflowStepTable($list_id, 0)
                           ->orderBy('wf.step_nr')
                           ->first();

        if (!$first_step)
        {
            // can't find step
           throw new Exceptions\DXCustomException(trans('task_form.err_no_wf_step'));
        }
        
        return $first_step->step_nr;
    }
    
    /**
     * Izveido jaunu uzdevumu, kuram nepieciešams lietotāja lēmums - uzdevumam uzstāda arī dokumenta meta datus
     * 
     * @param Array $steps Masīvs ar darbplūsmas soļiem (1 vai n, ja paralēlā saskaņošana)
     * @param integer $item_id Ieraksta ID
     */
    private function newAcceptanceTaskDocInfo($steps, $item_id)
    {
        $list_id = $steps[0]->list_id;
        
        $list_table = \App\Libraries\Workflows\Helper::getListTableName($list_id);
        
        $arr_meta_vals = \App\Libraries\Workflows\Helper::getMetaFieldVal($list_table, $list_id, $item_id);
        
        $reg_nr = $arr_meta_vals[self::REPRESENT_REG_NR];
        $info = $arr_meta_vals[self::REPRESENT_ABOUT];
        $item_empl_id = $arr_meta_vals[self::REPRESENT_EMPL];
        
        $this->insertAcceptanceTask($steps, $list_id, $item_id, $reg_nr, $info, $item_empl_id);
    }
    
    /**
     * Pārbauda, vai papildināšanas uzdevumam ir norādītas visas papildināmo lauku vērtības
     * 
     * @param Object $task_row Uzdevuma dati
     * @throws Exceptions\DXCustomException
     */
    private function validateDocFields($task_row)
    {        
        $fields = DB::table("dx_workflows_fields")->where('workflow_id','=',$task_row->step_id)->get();
        
        foreach($fields as $fld)
        {
            $list_row = DB::table("dx_lists")->where('id','=', $fld->list_id)->first();
            $obj_row = DB::table("dx_objects")->where('id','=',$list_row->object_id)->first();
            $fld_row = DB::table("dx_lists_fields")->where("id", "=", $fld->field_id)->first();
            
            $value_row = DB::table($obj_row->db_name)->select(DB::raw($fld_row->db_name . " as val"))->where('id','=',$task_row->item_id)->whereNotNull($fld_row->db_name)->first();
            
            if (!$value_row)
            {
                throw new Exceptions\DXCustomException(sprintf(trans('task_form.err_approve_field'), $fld_row->title_form));
            }
            
            // additional validation for some field types
            if ($fld_row->type_id == self::FIELD_TYPE_INT)
            {
                // Numurs
                if ($value_row->val == 0)
                {
                    throw new Exceptions\DXCustomException(sprintf(trans('task_form.err_approve_field_num'), $fld_row->title_form));
                }
            }
            
            if ($fld_row->type_id == self::FIELD_TYPE_LOOKUP)
            {
                $this->validateLookupAcceptance($fld_row, $value_row);
            }            
        }
    }
    
    /**
     * Pārbauda, vai saistītais dokuments ir norādīts un tas ir apstiprināts (ja tam ir darbplūsma definēa)
     * 
     * @param Object $fld_row Lauka objekts
     * @param integer $value_row Lauka vērtība
     * @throws Exceptions\DXCustomException
     */
    private function validateLookupAcceptance($fld_row, $value_row) {
        // Uzmeklēšana
        if ($value_row->val == 0)
        {
            throw new Exceptions\DXCustomException(sprintf(trans('task_form.err_approove_field_lookup'), $fld_row->title_form));
        }

        // Check if related list have workflow status field
        $rel_field = DB::table("dx_lists_fields")->where('list_id','=',$fld_row->rel_list_id)->where('db_name','=','dx_item_status_id')->first();
        if ($rel_field)
        {

            $rel_workflow = DB::table('dx_workflows_def')
                            ->select('id')
                            ->where('list_id', '=', $rel_field->list_id)
                            ->where('valid_from', '<=', date('Y-n-d'))
                            ->where(function($query) {
                                $query->whereNull('valid_to')
                                      ->orWhere('valid_to', '>=', date('Y-n-d'));
                            })
                            ->first();

            if ($rel_workflow)
            {
                // Check related item workflow status - must be approved

                // Get related item list row
                $rel_list_row = DB::table("dx_lists")->where('id','=', $rel_field->list_id)->first();

                // Get related item object table name
                $rel_obj_row = DB::table("dx_objects")->where('id','=',$rel_list_row->object_id)->first();

                // Check if statuss for related record is approved
                $approved = DB::table($rel_obj_row->db_name)->select(DB::raw($rel_field->db_name . " as val"))->where('id','=',$value_row->val)->where($rel_field->db_name,'=', self::WORKFLOW_STATUS_APPROVED)->first();
                if (!$approved)
                {
                    throw new Exceptions\DXCustomException(sprintf(trans('task_form.err_approve_lookup_approved'), $fld_row->title_form));
                }
            }
        }
    }
    
    /**
     * Atgriež uzdevuma datus.
     * Uzstāda arī aktuālās darbplūsmas ID
     * 
     * @param integer $task_id Uzdevuma ID
     * @param integer $employee_id Darbinieka (izpildītāja) ID
     * @return Array Masīvs ar uzdevuma datiem
     * @throws Exceptions\DXCustomException
     */
    private function getTaskRow($task_id, $employee_id) {
        // check if task belongs to user and can be updated
        $task_row = DB::table("dx_tasks")
                    ->where('id', '=', $task_id)
                    ->whereNull('task_closed_time')
                    ->where('task_employee_id', '=', $employee_id)
                    ->first();

        if (!$task_row)
        {
            throw new Exceptions\DXCustomException(trans('task_form.err_cant_edit_task'));
        }
        
        if ($task_row->step_id) {
            // Uzstādam aktuālās darbplūsmas ID, kas tiks izmantots visās saistītajās šīs klases metodēs
            $workflow = DB::table('dx_workflows')->where('id','=', $task_row->step_id)->first();
            $this->workflow_id = $workflow->workflow_def_id;
            $this->wf_info_id = $task_row->wf_info_id;
        }
        
        return $task_row;
    }
    
    /**
     * Izpilda uzdevumu - uzstāda transakciju un izsauc rekursīvo uzdevumu izpildi
     * 
     * @param integer $task_id Uzdevuma ID
     * @param boolean $is_yes Vai ir pozitīvs lēmums (1 - jā, 0 - nē)
     * @param string $comment Piezīmes par uzdevuma izpildi (obligātas noraidīšanas gadījumā)
     */
    private function performTask($task_id, $is_yes, $comment)
    {
        DB::transaction(function () use ($task_id, $is_yes, $comment) {            
            $this->performTaskRecursive($task_id, $is_yes, $comment,  Auth::user()->id);
        });
    }
    
     /**
     * Izpilda uzdevumu rekursīvi
     * 
     * @param integer $task_id Uzdevuma ID
     * @param boolean $is_yes Vai ir pozitīvs lēmums (1 - jā, 0 - nē)
     * @param string $comment Piezīmes par uzdevuma izpildi (obligātas noraidīšanas gadījumā)
     * @param integer $employee_id Darbinieka (izpildītāja) ID
     */
    private function performTaskRecursive($task_id, $is_yes, $comment, $employee_id) {
        
        $task_row = $this->getTaskRow($task_id, $employee_id);
        
        if ($task_row->task_type_id == self::TASK_TYPE_FILL_ACCEPT && $is_yes)
        {
            // We need to validate if required fields in document were filled
            $this->validateDocFields($task_row);
        }

        $this->updateTaskStatus($is_yes, $task_row, $comment);            

        if ($task_row->task_type_id == self::TASK_TYPE_INFO) {            
            return;// we process next steps only for non-informative tasks
        }
        
        // Anulē deleģētos vēl neizpildītos uzdevumus
        $this->cancelDelegatedTasks($task_row->id);
        
        $wf_info = DB::table('dx_workflows_info')->where('id', '=', $task_row->wf_info_id)->first();
        
        if (!$is_yes) {
            
            if ($wf_info->is_paralel_approve) {
                $this->cancelParalelApprovalTasks($task_id);
            }
        }

        if ($task_row->parent_task_id) {
            if ($is_yes) {
                if ($this->isDelegatedParalelTasks($task_row)) {
                    return; // tajā pašā līmenī ir vēl citi uzdevumi, kas ir procesā vai noraidīti, tāpēc automātiski izpildīt vecāka uzdevumu nevar
                }
                else {
                    $this->performTaskRecursive($task_row->parent_task_id, $is_yes, trans('task_form.comment_compleated'), $task_row->assigned_empl_id);
                    return; // izpildam vecāka uzdevumu ToDo: te potenciāli var būt pazīme pie uzdevuma to darīt/nedarīt automātiski..
                }
            }
            else {
                // noraidot deleģēto uzdevumu, deleģētājam ir jāizlemj ko darīt tālāk (var arī izlemt pats izpildīt uzdevumu tomēr.. vai deleģēt kādam citam)
                // tāpēc neko tālāk neprocesējam ToDo: te potenciāli var būt pazīme pie uzdevuma to darīt/nedarīt automātiski..
                return; 
            }
        }
        
        if ($task_row->wf_approv_id) {
            
            DB::table('dx_workflows_approve')
            ->where('id','=', $task_row->wf_approv_id)
            ->update(['is_done' => 1]);
            
            if ($is_yes && $wf_info->is_paralel_approve) {
                // pārbaudam vai ir vēl neizpildīti citi paralēli uzdevumi
                $other_task = DB::table('dx_tasks')->where('wf_info_id', '=', $wf_info->id)->whereNull('task_closed_time')->first();
                
                if ($other_task) {
                    return; // jāgaida kamēr citi izpildīs
                }
                
            }
        }
        
        // darbplūsma var turpināties, nosakam un izpildam nākamo soli
        $this->doTaskNextStep($task_row, $is_yes);
    }
    
    /**
     * Nosaka un izpilda darbplūsmas nākamo soli pēc uzdevuma pabeigšanas
     * 
     * @param type $task_row
     * @param type $is_yes
     */
    private function doTaskNextStep($task_row, $is_yes) {
        // get workflow current step
        $current_step = $this->getWorkflowStepTable($task_row->list_id, 0)
                        ->where('wf.id', '=', $task_row->step_id)
                        ->first();

        // check if exists paralel tasks with the same step nr and can we continue to next step or need to wait
        if ($this->getParalelTasksStatus($current_step, $is_yes, $task_row))
        {
            $approver = null;
            if ($task_row->wf_approv_id) {
                $approver = DB::table('dx_workflows_approve')
                             ->select('id')
                             ->where('is_done','=', 0)
                             ->where('workflow_info_id', '=', $task_row->wf_info_id)
                             ->orderBy('order_index')
                             ->first();
            }
            
            if (!$approver || !$is_yes) {
                $next_step_nr = 0;
                if ($is_yes)
                {
                    $next_step_nr = $current_step->yes_step_nr;
                }
                else
                {
                    $next_step_nr = $current_step->no_step_nr;
                }

                $steps = $this->getNextStep($next_step_nr, $current_step->list_id, $task_row->item_id, 1); // get next closest acceptance task (can be n paralel)
            }
            else {
                // Jāizpilda tas pats solis jo ir vēl citi manuāli iestatītie saskaņotāji
                $steps = $this->getNextStep($current_step->step_nr, $current_step->list_id, $task_row->item_id, 1);
            }
            
            if (count($steps) > 0){
                $this->newAcceptanceTask($task_row, $steps);
            }
            else {
                // darbplūsma ir pabeigta - nav vairāk soļu
                DB::table('dx_workflows_info')->where('id','=', $this->wf_info_id)->update(['end_time' => date("Y-m-d H:i:s"), 'end_user_id' => Auth::user()->id]);
            }
        }
    }
    
    /**
     * Uzstāda uzdevuma statusu uz izpildīts vai noraidīts
     * 
     * @param boolean $is_yes Pazīme, vai uzdevums ir izpildīts (true)/noraidīts (false)
     * @param object $task_row Uzdevuma objekts
     * @param string $comment Komentārs (obigātums noraidīšanas gaījumā tiek kontrolēts metodē doNo)
     */
    private function updateTaskStatus($is_yes, $task_row, $comment) {
        $task_status = self::TASK_STATUS_DONE; // Izpildits (allways for informative tasks)
        if (!$is_yes && $task_row->task_type_id != self::TASK_TYPE_INFO)
        {
            $task_status = self::TASK_STATUS_DENY; // Noraidits
        }

        // update task status
        DB::table("dx_tasks")
                ->where('id','=',$task_row->id)
                ->update(['task_closed_time' => date("Y-m-d H:i:s"), 'task_status_id' => $task_status, 'task_comment' => $comment]);
    }
    
    /**
     * Pārbauda, vai citi deleģētie uzdevumi ir pozitīvi izpildīti (tav noraidīti un nav deleģēti vai procesā)
     * 
     * @param object $task_row Esošā uzdevuma objekts
     * @return boolean Atgriež true, ja ir kāds paralēls deleģēts uzdevums (noraidīts vai procesā, un atgriež false, ja nav neviens paralēls uzdevums procesā vai noraidīts
     */
    private function isDelegatedParalelTasks($task_row) {        
        return (DB::table('dx_tasks')
                       ->where('parent_task_id', '=', $task_row->parent_task_id)
                       ->where('id', '!=', $task_row->id)
                       ->where(function($query) {
                           $query->where('task_status_id', '=', self::TASK_STATUS_DENY)
                                 ->orWhere('task_status_id', '=', self::TASK_STATUS_DELEGATE)
                                 ->orWhere('task_status_id', '=', self::TASK_STATUS_PROCESS);
                       })
                       ->count() > 0);        
    }
    
    /**
     * Saistīto (deleģēto) uzdevumu anulēšana (rekursīvi)
     * Izpildot galveno uzdevumu, deleģētie uzdevumi tiek anulēti.
     * 
     * @param type $parent_task_id
     */
    private function cancelDelegatedTasks($parent_task_id) {        
        $tasks = DB::table("dx_tasks")
                ->where('parent_task_id','=',$parent_task_id)
                ->where(function($query) {
                    $query->where('task_status_id','=', self::TASK_STATUS_PROCESS)
                          ->orWhere('task_status_id','=', self::TASK_STATUS_DELEGATE);                            
                })
                ->get();
        
        foreach($tasks as $task) {
            DB::table("dx_tasks")
            ->where('id', '=', $task->id)
            ->update([
            'task_closed_time' => date("Y-m-d H:i:s"), 
            'task_status_id' => self::TASK_STATUS_CANCEL, 
            'task_comment' => sprintf(trans('task_form.comment_anulated'), Auth::user()->display_name)
            ]);
            
            $this->cancelDelegatedTasks($task->id);
        }
    }
    
    /**
     * Atceļ manuāli iestatītos paralēlās saskaņošanas uzdevumus (jo kāds noraidīja savu uzdevumu) un to deleģētos uzdevumus
     * @param integer $task_id Noraidītā uzdevuma ID
     */
    private function cancelParalelApprovalTasks($task_id) {
        $tasks = DB::table("dx_tasks")
                ->where('id','!=',$task_id)
                ->where('wf_info_id', '=', $this->wf_info_id)
                ->where(function($query) {
                    $query->where('task_status_id','=', self::TASK_STATUS_PROCESS)
                          ->orWhere('task_status_id','=', self::TASK_STATUS_DELEGATE);                            
                })
                ->get();
        
        foreach($tasks as $task) {
            DB::table("dx_tasks")
            ->where('id', '=', $task->id)
            ->update([
            'task_closed_time' => date("Y-m-d H:i:s"), 
            'task_status_id' => self::TASK_STATUS_CANCEL, 
            'task_comment' => sprintf(trans('task_form.comment_anulated'), Auth::user()->display_name)
            ]);
            
            $this->cancelDelegatedTasks($task->id);
        }
    }
    
    /**
     * Prbauda vai ir paralēlā saskaņošana. Ja ir, vai nav kāds noraidījis vai arī ir jāgaida kāda cita saskaņojums
     * 
     * @param Object $current_step Darbplūsmas pašreizējais solis
     * @param integer $is_yes Vai lēmums ir pozitīvs
     * @param Object $task_row Uzdevuma objekts
     * @return boolean Pazīme, vai ir paralēlā saskaņošana (vai arī jāgaida, ka kāds vēl saskaņos): true - nav jāgaida, false - ir jāgaida
     * @throws Exceptions\DXCustomException
     */
    private function getParalelTasksStatus($current_step, $is_yes, $task_row)
    {
        $steps = $this->getWorkflowStepTable($current_step->list_id, $current_step->step_nr)
                 ->where('wf.id','!=',$current_step->id)
                 ->get();
        
        if (count($steps) == 0)
        {
            return true; // no paralel steps
        }
        
        // loop all paralel steps and check tasks statuses
        foreach($steps as $step)
        {
            $task = DB::table('dx_tasks')
                    ->where('step_id', '=',$step->id)
                    ->where('item_id','=',$task_row->item_id)
                    ->where('list_id', '=', $current_step->list_id)
                    ->whereNull('parent_task_id') // pārbaudam tikai galveno uzdevumu statusus
                    ->first();
            
            if (!$task)
            {
                throw new Exceptions\DXCustomException(trans('task_form.err_no_paralel_step_task'));
            }

            if ($task->task_status_id == self::TASK_STATUS_PROCESS || $task->task_status_id == self::TASK_STATUS_DELEGATE)
            {
                if (!$is_yes)
                {
                    // lets cancel task
                    DB::table("dx_tasks")
                    ->where('id','=',$task->id)
                    ->update(['task_closed_time' => date("Y-m-d H:i:s"), 'task_status_id' => self::TASK_STATUS_CANCEL, 'task_comment' => trans('task_form.comment_somebody_rejected')]);
                }
                else
                {
                    return false; // it is needed to wait to finish other tasks
                }
            }
            
        }
        
        return true;
    }
    
    /**
     * Izveido jaunu uzdevumu, kuram nepieciešams lietotāja lēmums
     * @param Object $task_row Uzdevuma objekts
     * @param Array $steps Masīvs ar darbplūsmas soļiem
     */
    private function newAcceptanceTask($task_row, $steps)
    {
        $this->insertAcceptanceTask($steps, $task_row->list_id, $task_row->item_id, $task_row->item_reg_nr, $task_row->item_info, $task_row->item_empl_id);
    }
    
    /**
     * Izveido uzdevumus, kuriem nepieciešams lietotāja lēmums (saskaņot, izpildīt utt)
     * 
     * @param Array   $steps        Masīvs ar darbplūsmas soļiem (1 vai vairāki, ja paralēlās saskāņošana)
     * @param integer $list_id      Reģistra ID
     * @param integer $item_id      Ieraksta ID
     * @param string  $item_reg_nr  Ieraksta (dokumenta) reģ. nr.
     * @param string  $item_info    Dokumenta saturs (apraksts)
     * @param integer $item_empl_id Darbinieka ID (ja uzdevums ir saistīts ar darbinieka objektu)
     * @throws Exceptions\DXCustomException
     */
    private function insertAcceptanceTask($steps, $list_id, $item_id, $item_reg_nr, $item_info, $item_empl_id)
    {        
        $yes_step_nr = 0;
        $no_step_nr = 0;
        foreach($steps as $step_row)
        {
            if ( !($step_row->task_type_id < self::TASK_TYPE_SET_VAL || $step_row->task_type_id == self::TASK_TYPE_INFO) )
            {
                throw new Exceptions\DXCustomException(trans('task_form.err_wrong_wf_definition'));
            }
            
            if ($yes_step_nr == 0)
            {
                $yes_step_nr = $step_row->yes_step_nr;
            }
            
            if ($yes_step_nr != $step_row->yes_step_nr)
            {
                throw new Exceptions\DXCustomException(trans('task_form.err_wrong_yes_settings'));
            }
            
            if ($no_step_nr == 0)
            {
                $no_step_nr = $step_row->no_step_nr;
            }
            
            if ($no_step_nr != $step_row->no_step_nr)
            {
                throw new Exceptions\DXCustomException(trans('task_form.err_wrong_no_settings'));
            }
            
            $performer_obj = \App\Libraries\Workflows\Performers\PerformerFactory::build_performer($step_row, $item_id, $this->wf_info_id);            
                       
            $performers = $performer_obj->getEmployees();
            
            $resolution_text = $this->getTaskResolution($step_row, $item_id);
            
            foreach($performers as $performer) {                
                
                // Acceptance task
                $task_id = DB::table('dx_tasks')->insertGetId([
                    'due_date' => $performer["due"], 
                    'task_details'=> $resolution_text, 
                    'substit_info' => (strlen($performer["subst_data"]["subst_info"]) > 0) ? $performer["subst_data"]["subst_info"] : null , 
                    'task_created_time' => date("Y-m-d H:i:s"), 
                    'list_id' => $list_id, 
                    'item_id' => $item_id, 
                    'item_reg_nr' => $item_reg_nr, 
                    'item_info' => $item_info, 
                    'task_type_id' => $step_row->task_type_id, 
                    'task_status_id' => self::TASK_STATUS_PROCESS, 
                    'task_employee_id' => $performer["empl_id"], 
                    'step_id' => $step_row->id, 
                    'wf_info_id' => $this->wf_info_id,
                    'wf_approv_id' => $performer["wf_approv_id"],
                    'item_empl_id' => $item_empl_id
                ]);
            
                $this->sendNewTaskEmail([
                    'email' => $performer["subst_data"]["email"],
                    'subject' => sprintf(trans('task_email.subject'), trans('index.app_name')),
                    'task_type' => DB::table('dx_tasks_types')->select('title')->where('id', '=', $step_row->task_type_id)->first()->title,
                    'task_details' => $resolution_text,
                    'assigner' => trans('task_email.assigner_wf'),
                    'due_date' => $performer["due"],
                    'list_title' => DB::table('dx_lists')->select('list_title')->where('id', '=', $list_id)->first()->list_title,
                    'doc_id' => $item_id,
                    'doc_about' => $item_info,
                    'task_id' => $task_id,
                    'date_now' => date('Y-n-d H:i:s')
                ]);
            }
        }
    }
    
    /**
     * Izgūst uzdevuma rezolūcijas tekstu - kā primāro izmanto dokumentā norādīto rezolūcijas tekstu
     * 
     * @param Object $step_row Darbplūsmas soļa objekts
     * @param integer $item_id Ieraksta ID
     * @return string Rezolūcijas teksts
     */
    private function getTaskResolution($step_row, $item_id) {
        $resolution_txt = "";
        if ($step_row->resolution_field_id) {
                
            $fld_row = DB::table("dx_lists_fields")->where('id', '=', $step_row->resolution_field_id)->first();

            \App\Libraries\Workflows\Helper::validateResolutionField($fld_row);

            $resolution_txt = \App\Libraries\Workflows\Helper::getDocEmplValue($step_row->list_id, $item_id, $fld_row);
            
        }
        
        if (strlen($resolution_txt) == 0) {
            $resolution_txt = $step_row->notes;
        }
        
        return $resolution_txt;
    }
    
    /**
     * Uzstāda ieraksta lauka vērtību (ja darpblūsmas soļa veids ir uzstādīt vērtību)
     * 
     * @param Object $step_row Darbplūsmas solis
     * @param integer $item_id Ieraksta ID
     * @param integer $recursion_nr Rekursijas iterācijas numurs
     * @return Object Nākamā darbplūsmas soļa objekts
     */
    private function stepSetValue($step_row, $item_id, $recursion_nr)
    {             
        // get list db table name
        $list_row = DB::table("dx_lists")->where("id", "=", $step_row->list_id)->first();
        $obj_row = DB::table("dx_objects")->where("id", "=", $list_row->object_id)->first();
        
        // get table field name
        $fld_row = DB::table("dx_lists_fields")->where("id", "=", $step_row->field_id)->first();
        
        // update item field value
        DB::table($obj_row->db_name)->where('id', '=', $item_id)->update([$fld_row->db_name => $step_row->field_value]);
        
        // go to yes step
        return $this->getNextStep($step_row->yes_step_nr, $step_row->list_id, $item_id, $recursion_nr);
    }
    
    /**
     * Nosaka nākamo soli atkarībā no kritērija vērtības
     * 
     * @param Object $step_row Pašreizējā darbplūsmas soļa objekts
     * @param integer $item_id Ieraksta ID
     * @param integer $recursion_nr Rekursijas iterācijas numurs
     * @return Object Nākamā soļa objekts
     */
    private function stepCriteria($step_row, $item_id, $recursion_nr)
    {                    
        // get list db table name
        $list_row = DB::table("dx_lists")->where("id", "=", $step_row->list_id)->first();
        $obj_row = DB::table("dx_objects")->where("id", "=", $list_row->object_id)->first();
        
        // get table field name
        $fld_row = DB::table("dx_lists_fields")->where("id", "=", $step_row->field_id)->first();
        
         // get field value
        $fld_val_row = DB::table($obj_row->db_name)->where('id', '=', $item_id)->select(DB::raw($fld_row->db_name . " as val"))->first();
        
        $next_step_nr = 0;
        if ($this->getCriteriaResult($fld_val_row->val, $step_row->field_operation_id, $step_row->field_value))
        {
            $next_step_nr = $step_row->yes_step_nr;
        }
        else
        {
            $next_step_nr = $step_row->no_step_nr;
        }
        
        // go to next step
        return $this->getNextStep($next_step_nr, $step_row->list_id, $item_id, $recursion_nr);
    }
    
     /**
     * Nosaka nākamo soli atkarībā no kritērija - vai ir iestatīta manuālā saskaņošana
     * 
     * @param Object $step_row Pašreizējā darbplūsmas soļa objekts
     * @param integer $item_id Ieraksta ID
     * @param integer $recursion_nr Rekursijas iterācijas numurs
     * @return Object Nākamā soļa objekts
     */
    private function stepWFCriteria($step_row, $item_id, $recursion_nr)
    {                    
        $wf_info = DB::table('dx_workflows_approve')->where('workflow_info_id', '=', $this->wf_info_id)->first();

        if ($wf_info) {
            // ir iestatīta saskaņošana manuāli
            $next_step_nr = $step_row->yes_step_nr;
        }
        else {
            $next_step_nr = $step_row->no_step_nr;
        }
        
        // go to next step
        return $this->getNextStep($next_step_nr, $step_row->list_id, $item_id, $recursion_nr);
    }
    
    /**
     * Informatīvā uzdevuma soļa izpilde
     * 
     * @param Array $steps Masīvs ar darbplūsmas soļiem
     * @param integer $item_id Ieraksta ID
     * @param integer $recursion_nr Rekursijas iterācijas numurs
     * @return Object Nākamā soļa objekts
     */
    private function stepInformativeTask($steps, $item_id, $recursion_nr)
    {
        $this->newAcceptanceTaskDocInfo($steps, $item_id); // we use this method because we need to extract meta info from document
        
        $step_row = $steps[0];
        
        // go to yes step, we don't wait decision from informative task
        return $this->getNextStep($step_row->yes_step_nr, $step_row->list_id, $item_id, $recursion_nr);
    }
    
    /**
     * Procesē nākamo darbplūsmas soli (rekursīvi)
     * 
     * @param integer $step_nr Soļa numurs
     * @param integer $list_id Reģistra ID
     * @param integer $item_id Ieraksta ID
     * @param integer $recursion_nr Rekursijas iterācijas numurs
     * @return Object Soļa objekts vai null, ja nav nākamā soļa
     * @throws Exceptions\DXCustomException
     */
    private function getNextStep($step_nr, $list_id, $item_id, $recursion_nr)
    {
        if ($step_nr == 0)
        {
            return null;
        }
        
        if ($recursion_nr > 100) // anti infinity loop check
        {
            throw new Exceptions\DXCustomException(sprintf(trans('task_form.err_infinite_loop'), $recursion_nr));
        }
        
        $next_step = $this->getWorkflowStepTable($list_id, $step_nr)->first();
            
        if ($next_step)
        {
            if ($next_step->task_type_id < self::TASK_TYPE_SET_VAL) // 1 - saskaņot; 2 - izpildīt; 3 - papildināt un saskaņot;
            {                
                return $this->getWorkflowStepTable($list_id, $step_nr)->get(); // acceptance task (can be n paralel)
            }
            else if ($next_step->task_type_id == self::TASK_TYPE_SET_VAL)
            { 
                // set value task
                return $this->stepSetValue($next_step, $item_id, $recursion_nr+1);
            }
            else if ($next_step->task_type_id == self::TASK_TYPE_CRITERIA)
            {
                // criteria task
                return $this->stepCriteria($next_step, $item_id, $recursion_nr+1);
            }
            else if ($next_step->task_type_id == self::TASK_TYPE_WF_CRITERIA)
            {
                // Workflow criteria task
                return $this->stepWFCriteria($next_step, $item_id, $recursion_nr+1);
            }
            else if ($next_step->task_type_id == self::TASK_TYPE_INFO)
            {
                // make informative task
                $steps = $this->getWorkflowStepTable($list_id, $step_nr)->get();
                
                return $this->stepInformativeTask($steps, $item_id, $recursion_nr);
            }
            else
            {
                // ToDo: implement other tasks types
                throw new Exceptions\DXCustomException(sprintf(trans('task_form.err_wrong_task_type'), $list_id, $next_step->task_type_id ));
            }
        }
        else
        {
            throw new Exceptions\DXCustomException(sprintf(trans('task_form.err_wrong_task_type'), $list_id, $step_nr ));
        }
    }
    
    /**
     * Atgriež darbplūsmas soļa(u) Laravel tabulas objektu
     * 
     * @param integer $list_id Reģistra ID
     * @param integer $step_nr Soļa numurs
     * @return Object Darbplūsmas Laravel tabulas objekts
     */
    private function getWorkflowStepTable($list_id, $step_nr) {
        $tb =   DB::table("dx_workflows as wf")
                ->select('wf.*', 'tp.code as perform_code', 'r.title as role_title')
                ->leftJoin('dx_tasks_perform as tp', 'wf.task_perform_id', '=', 'tp.id')
                ->leftJoin('dx_roles as r', 'wf.role_id', '=', 'r.id')
                ->where('wf.list_id', '=', $list_id)
                ->where('wf.workflow_def_id', '=', $this->workflow_id);
        
        if ($step_nr > 0) {
            $tb->where('wf.step_nr', '=', $step_nr);
        }
        
        return $tb;
    }
    
    private function getCriteriaResult($item_val, $operation_id, $condition_val)
    {
         switch ($operation_id) {
                case 1:
                    return ($item_val == $condition_val);
                case 2:
                    return ($item_val != $condition_val);
                case 3:
                    return ($item_val > $condition_val);
                case 4:
                    return ($item_val < $condition_val);
                case 5:
                    return (strpos(str_replace(" ", "", "," . $condition_val . ","), "," . $item_val . ",") > 0);
                case 6:
                    return isNull($item_val);
                case 7:
                    return !isNull($item_val);
                default:
                    throw new Exceptions\DXCustomException(sprintf(trans('task_form.err_wrong_operation'), $operation_id));
        }
    }
}
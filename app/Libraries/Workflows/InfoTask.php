<?php

namespace App\Libraries\Workflows
{
    use App\Http\Controllers\Controller;
    use DB;
    use App\Exceptions;
    use App\Http\Controllers\TasksController;
    use Auth;
    use App\Jobs\SendTaskEmail;
    use App\Libraries\Rights;
    
    /**
     * Informative task creation class
     */
    class InfoTask extends Controller
    {
        /**
         * Register item ID
         * @var integer
         */
        private $item_id = 0;
        
        /**
         * Register ID
         * @var integer 
         */
        private $list_id = 0;
        
        /**
         * Indicates if validation handlers must be executed - check rights and validate data
         * @var boolean 
         */
        private $is_validation_on = true;
        
        /**
         * Informative task title
         * @var string 
         */
        private $task_type_title = "";
        
        /**
         * Register title
         * @var string
         */
        private $list_title = "";
        
        /**
         * Array with item meta data values - reg nr, about and employee id
         * @var Array 
         */
        private $arr_meta_vals = [];
        
        /**
         * Constructs info task class
         * Constructor sets common attributes for given list and item
         * 
         * @param integer $list_id Register ID
         * @param integer $item_id Item ID
         */
        public function __construct($list_id, $item_id, $is_validation_on = true)
        {
            $this->item_id = $item_id;            
            $this->list_id = $list_id;
            $this->is_validation_on = $is_validation_on;
            
            if ($this->is_validation_on) {
                $this->checkRights();
            }
            
            $this->setDefaults();

        }
        
        /**
         * Creates informative task and send notification for given employee
         * 
         * @param integer $employee_id Employee ID
         * @param string $employee_email Employee email
         * @param string $task_details Informative task details (description)
         */
        public function makeTask($employee_id, $employee_email, $task_details) {
            if ($this->is_validation_on) {            
                $this->validateExisting($employee_id);
            }
            
            $new_task_id = DB::table('dx_tasks')->insertGetId([
                    'assigned_empl_id' => Auth::user()->id,
                    'task_details' => $task_details,
                    'created_user_id' => Auth::user()->id,
                    'created_time' => date('Y-n-d H:i:s'),
                    'modified_user_id' => Auth::user()->id,
                    'modified_time' => date('Y-n-d H:i:s'),
                    'list_id' => $this->list_id,
                    'item_id' => $this->item_id,
                    'item_reg_nr' => $this->arr_meta_vals[TasksController::REPRESENT_REG_NR],
                    'item_info' => $this->arr_meta_vals[TasksController::REPRESENT_ABOUT],
                    'task_type_id' => TasksController::TASK_TYPE_INFO,
                    'task_created_time' => date('Y-n-d H:i:s'),
                    'task_status_id' => TasksController::TASK_STATUS_PROCESS,
                    'task_employee_id' => $employee_id,
                    'item_empl_id' => $this->arr_meta_vals[TasksController::REPRESENT_EMPL]
            ]);
            
            if ($employee_email) {
                $this->sendMail($employee_id, $employee_email, $task_details, $new_task_id);
            }
        }
        
        /**
         * Send email about informative task
         * 
         * @param integer $employee_id Employee ID
         * @param string $employee_email Employee email
         * @param string $task_details Informative task details (description)
         * @param integer $new_task_id Newly created task ID
         */
        private function sendMail($employee_id, $employee_email, $task_details, $new_task_id) {
            
            $arr_data = [
                'email' => $employee_email,
                'subject' => sprintf(trans('task_email.subject'), trans('index.app_name')),
                'task_type' => $this->task_type_title,
                'task_details' => $task_details,
                'assigner' => Auth::user()->display_name,
                'due_date' => null,
                'list_title' => $this->list_title,
                'doc_id' => $this->item_id,
                'doc_about' => $this->arr_meta_vals[TasksController::REPRESENT_ABOUT],
                'task_id' => $new_task_id,
                'date_now' => date('Y-n-d H:i:s')
            ];
            
            $this->dispatch(new SendTaskEmail($arr_data));
        }
        
        /**
         * Set's default values used for informative tasks creation and notify email sending
         */
        private function setDefaults() {
            $list_table = \App\Libraries\Workflows\Helper::getListTableName($this->list_id);
        
            $this->arr_meta_vals = \App\Libraries\Workflows\Helper::getMetaFieldVal($list_table, $this->list_id, $this->item_id);
            
            $this->task_type_title = DB::table('dx_tasks_types')
                                     ->select('title')
                                     ->where('id', '=', TasksController::TASK_TYPE_INFO)
                                     ->first()
                                     ->title;
            
            $this->list_title = DB::table('dx_lists')
                            ->select('list_title')
                            ->where('id', '=', $this->list_id)
                            ->first()
                            ->list_title;
        }
        
        /**
         * Checks if user have rights on list/document to give it to another user
         * 
         * @throws Exceptions\DXCustomException
         */
        private function checkRights() {
            $right = Rights::getRightsOnList($this->list_id);

            if ($right == null) {
                if (!\App\Libraries\Workflows\Helper::isRelatedTask($this->list_id, $this->item_id)) {
                    throw new Exceptions\DXCustomException(trans('task_form.err_no_list_rights'));
                }
            }    
        }
        
        /**
         * Validates if employee allready had informatiove task on this item
         * 
         * @param integer $employee_id Employee ID
         * @throws Exceptions\DXCustomException
         */
        private function validateExisting($employee_id) {
            $task = DB::table('dx_tasks')
                    ->where('list_id', '=', $this->list_id)
                    ->where('item_id', '=', $this->item_id)
                    ->where('task_employee_id', '=', $employee_id)
                    ->where('task_type_id', '=', self::TASK_TYPE_INFO)
                    ->first();

            if ($task) {
                throw new Exceptions\DXCustomException(trans('task_form.err_allready_informed'));
            }
        }
    }

}
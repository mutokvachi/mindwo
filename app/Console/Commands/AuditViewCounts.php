<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Exceptions;
use Auth;
use Log;

/**
 * Calculates time off for given employee
 */
class AuditViewCounts extends Command
{

    protected $signature = 'mindwo:audit_view';
    protected $description = 'Audits record counts in views defined for monitoring';

    /**
     * User (ID) who executes thi process
     * @var integer
     */
    private $exec_user_id = 1; // super admin by default

    /**
     * Notification email receivers
     * @var array 
     */
    private $arr_emails = [];

    /**
     * Responsible employees to whom send notification emails
     * @var array 
     */
    private $arr_empl = [];

    /**
     * Views which have record count > 0 and for which is notification needed
     * @var array
     */
    private $arr_views = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Auth::loginUsingId($this->exec_user_id, true);

        $views = DB::table('dx_views')->where('is_for_monitoring', '=', 1)->get();

        foreach ($views as $view) {
            $this->arr_emails = [];
            $view_obj = new \App\Libraries\View($view->list_id, $view->id, $this->exec_user_id);

            $view_sql = $view_obj->get_view_sql();

            if (strlen($view_obj->err) > 0) {
                throw new Exceptions\DXCustomException($view_obj->err);
            }

            $view_data = DB::select($view_sql);

            DB::transaction(function () use ($view, $view_data)
            {
                DB::table('dx_views_audit')->where('view_id', '=', $view->id)->whereDate('audit_time', '=', date('Y-n-d'))->delete();

                DB::table('dx_views_audit')->insert([
                    'view_id' => $view->id,
                    'audit_time' => date('Y-n-d H:i:s'),
                    'record_count' => count($view_data)
                ]);
            });

            if (!$view->is_email_sending || count($view_data) == 0) {
                continue;
            }

            if ($view->field_id) {
                $this->notifyEmployees($view_data, $view);
            }

            if ($view->email_receivers) {
                $this->arr_emails = explode(";", $view->email_receivers);
            }

            if ($view->role_id) {
                $this->fillRoleEmployees($view->role_id);
            }

            if (count($this->arr_emails)) {
                if ($view->is_detailed_notify) {
                    $this->notifyDetailed($view_data, $view);
                }
                else {
                    array_push($this->arr_views, ['view' => $view, 'emails' => $this->arr_emails, 'count' => count($view_data)]);
                }
            }
        }

        if (count($this->arr_views) > 0) {
            $this->sendGeneralNotify();
        }

        $this->info('Audit done!');
    }
    
    /**
     * Send general notification regarding views records
     */
    private function sendGeneralNotify()
    {
        $this->arr_emails = [];
        $cur_key = -1;
        
        foreach ($this->arr_views as $info) {

            foreach ($info['emails'] as $em) {
                if ($cur_key < 0 || $this->arr_emails[$cur_key]['email'] != $em) {
                    $cur_key = $this->findEmailKey($em);
                }
                
                $is_added = false;
                foreach($this->arr_emails[$cur_key]['views'] as $view) {
                    if ($view['view_id'] == $info['view']->id) {
                        $is_added = true;
                        
                        break;
                    }
                }
                
                if (!$is_added) {
                    array_push($this->arr_emails[$cur_key]['views'], [
                        'view_id' => $info['view']->id,
                        'count' => $info['count'],
                        'view_title' => $info['view']->title,
                    ]);
                }
            }
        }
                
        foreach ($this->arr_emails as $notify) {
            $arr_data = [];
            $arr_data['email'] = $notify['email'];
            $arr_data['subject'] = sprintf(trans('monitor_email.' . (count($this->arr_views) > 1 ? 'intro_general_n' : 'intro_general_1')), count($this->arr_views), trans('index.app_name'));
            $arr_data['items'] = $notify['views'];
            
            dispatch(new \App\Jobs\SendMonitoringMail($arr_data, 'emails.monitor_general'));
        }
    }

    /**
     * Checks if email is added in array. If not added the appends
     * 
     * @param string $email Email to check/add
     * @return integer Array key of current email element
     */
    function findEmailKey($email)
    {
        foreach ($this->arr_emails as $key => $em_is) {
            if ($em_is['email'] == $email) {
                return $key;
            }
        }

        array_push($this->arr_emails, ['email' => $email, 'views' => array()]);
        return count($this->arr_emails) - 1;
    }

    /**
     * Fill emails array with employees from given role
     * 
     * @param integer $role_id Role ID
     */
    private function fillRoleEmployees($role_id)
    {
        $employees = DB::table('dx_users_roles as ur')
                ->select('u.email')
                ->leftJoin('dx_users as u', 'ur.user_id', '=', 'u.id')
                ->where('ur.role_id', '=', $role_id)
                ->get();

        foreach ($employees as $empl) {
            array_push($this->arr_emails, $empl->email);
        }
    }

    /**
     * Prepare employees to whom send notification and send notification email
     * 
     * @param object $view_data View data rows
     * @param object $view View row
     */
    private function notifyEmployees($view_data, $view)
    {
        $this->arr_empl = [];

        $list_table = \App\Libraries\Workflows\Helper::getListTableName($view->list_id);

        $cur_empl_key = -1;

        foreach ($view_data as $row) {

            $data_empl_id = $this->getMetaVal($list_table, $view, \App\Http\Controllers\TasksController::REPRESENT_RESPONSIBLE_EMPL, $row->id);
            if (!$data_empl_id) {
                continue;
            }

            if ($cur_empl_key < 0 || $this->arr_empl[$cur_empl_key]['user_id'] != $data_empl_id) {
                $cur_empl_key = $this->getCurEmplKey($data_empl_id);
            }

            array_push($this->arr_empl[$cur_empl_key]['items'], [
                'item_id' => $row->id,
                'about' => $this->getMetaVal($list_table, $view, \App\Http\Controllers\TasksController::REPRESENT_ABOUT, $row->id),
                'reg_nr' => $this->getMetaVal($list_table, $view, \App\Http\Controllers\TasksController::REPRESENT_REG_NR, $row->id),
            ]);
        }

        if (count($this->arr_empl)) {
            $this->sendDetailedNotify($view, $this->arr_empl);
        }
    }
    
    /**
     * Prepares detailed info and adds it to each email for which notification will be sent
     * @param object $view_data View data rows
     * @param object $view View data (row from table dx_views)
     */
    private function notifyDetailed($view_data, $view)
    {
        $arr_em = [];
        
        foreach($this->arr_emails as $em){
            array_push($arr_em, ['email' => $em, 'items' => array()]);
        }
        
        $list_table = \App\Libraries\Workflows\Helper::getListTableName($view->list_id);

        foreach ($view_data as $row) {
            
            foreach($arr_em as $key=>$em){
                
                array_push($arr_em[$key]['items'], [
                    'item_id' => $row->id,
                    'about' => $this->getMetaVal($list_table, $view, \App\Http\Controllers\TasksController::REPRESENT_ABOUT, $row->id),
                    'reg_nr' => $this->getMetaVal($list_table, $view, \App\Http\Controllers\TasksController::REPRESENT_REG_NR, $row->id),
                ]);
            }
        }
        
        if (count($arr_em)) {
            $this->sendDetailedNotify($view, $arr_em);
        }
    }

    /**
     * Finds employee in array - returns array key
     * If not found - adds employee in array
     * 
     * @param integer $empl_id Employee ID
     * @return integer Array key
     */
    private function getCurEmplKey($empl_id)
    {
        foreach ($this->arr_empl as $key => $empl) {
            if ($empl['user_id'] == $empl_id) {
                return $key;
            }
        }

        $user = DB::table('dx_users')->where('id', '=', $empl_id)->first();
        array_push($this->arr_empl, ['user_id' => $empl_id, 'items' => array(), 'email' => $user->email]);

        return count($this->arr_empl) - 1;
    }

    /**
     * Sends notifications to responsible employees
     * 
     * @param object $view View row
     * @param array $arr Array with employees emails
     */
    private function sendDetailedNotify($view, $arr)
    {        
        // get default view for list
        $def_view = DB::table('dx_views')->where('list_id', '=', $view->list_id)->where('is_default', '=', 1)->where('is_hidden_from_main_grid', '=', 0)->first();
        
        $view_id = ($def_view) ? $def_view->id : $view->id;
        
        foreach ($arr as $empl) {

            $arr_data = [];
            $arr_data['email'] = $empl['email'];
            $arr_data['subject'] = $view->title . ": " . count($empl['items']);
            $arr_data['items'] = $empl['items'];
            $arr_data['view_title'] = $view->title;
            $arr_data['view_id'] = $view_id;

            dispatch(new \App\Jobs\SendMonitoringMail($arr_data, 'emails.monitor_detailed'));
        }
    }
    
    /**
     * Retrieve value from table field
     * 
     * @param string $table_name Table name
     * @param object $view View row (table dx_views)
     * @param integer $fld_represent Representing field type ID
     * @param integer $item_id Item ID
     * @return mixed Field value
     * @throws Exceptions\DXNoRepresentField
     */
    private static function getMetaVal($table_name, $view, $fld_represent, $item_id)
    {
        $fld_row = DB::table('dx_views_fields as vf')
                ->select("vf.field_id")
                ->where('vf.view_id', '=', $view->id)
                ->where('vf.represent_id', '=', $fld_represent)
                ->first();

        if (!$fld_row) {
            throw new Exceptions\DXNoRepresentField();
        }

        $fld_val_row = DB::table("dx_lists_fields")->where("id", "=", $fld_row->field_id)->first();

        $val_row = DB::table($table_name)->select(DB::raw($fld_val_row->db_name . " as val"))->where('id', '=', $item_id)->first();

        return $val_row->val;
    }

}

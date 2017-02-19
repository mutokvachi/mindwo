<?php

namespace App\Libraries\Workflows
{

    use DB;
    use App\Exceptions;
    use Auth;
    use Log;
    
    /**
     * Darbplūsmu palīgfunkciju klase
     */
    class Helper
    {

        /**
         * Izgūst dokumenta lauka vērtību, kura tiks saglabāta pie uzdevuma
         * Pēc noklusēšanas centīsies iegūt meta informāciju no dx_doc_agreg atbilstošajiem laukiem. Ja neizdodas, tad no dokumenta laukiem, kas iestatīti darbplūsmas skatā
         * 
         * @param string    $table_name     Tabulas nosaukums
         * @param integer   $list_id        Reģistra ID
         * @param integer   $item_id        Ieraksta ID
         * @return array                    Masīvs ar dokumenta lauku vērtībām (reģ. nr un apraksts)
         */
        public static function getMetaFieldVal($table_name, $list_id, $item_id)
        {
            $doc_agreg = DB::table('dx_doc_agreg')
                         ->where('list_id', '=', $list_id)
                         ->where('item_id', '=', $item_id)
                         ->first();
            
            $arr_vals = [];
            
            if ($doc_agreg) {                
                if ($doc_agreg->description) {
                    $arr_vals[\App\Http\Controllers\TasksController::REPRESENT_ABOUT] = $doc_agreg->description;
                }
                else {
                    $arr_vals[\App\Http\Controllers\TasksController::REPRESENT_ABOUT] = Helper::getMetaValByField($table_name, $list_id, \App\Http\Controllers\TasksController::REPRESENT_ABOUT, $item_id);
                }

                if ($doc_agreg->reg_nr) {
                    $arr_vals[\App\Http\Controllers\TasksController::REPRESENT_REG_NR] = $doc_agreg->reg_nr;
                }
                else {
                    $arr_vals[\App\Http\Controllers\TasksController::REPRESENT_REG_NR] = Helper::getMetaValByField($table_name, $list_id, \App\Http\Controllers\TasksController::REPRESENT_REG_NR, $item_id);
                }
            }
            else {
                $arr_vals[\App\Http\Controllers\TasksController::REPRESENT_ABOUT] = Helper::getMetaValByField($table_name, $list_id, \App\Http\Controllers\TasksController::REPRESENT_ABOUT, $item_id);
                $arr_vals[\App\Http\Controllers\TasksController::REPRESENT_REG_NR] = Helper::getMetaValByField($table_name, $list_id, \App\Http\Controllers\TasksController::REPRESENT_REG_NR, $item_id);   
            }
            
            $arr_vals = Helper::setEmplRepresent($arr_vals, $table_name, $list_id, $item_id);
            
            return $arr_vals;
        }
        
        /**
         * Set value for employee representation field
         * 
         * @param array $arr_vals Representation array
         * @param string $table_name Table name where value can be found
         * @param integer $list_id Register ID
         * @param integer $item_id Item ID
         * @return array Representation array
         * @throws \App\Libraries\Workflows\Exception
         */
        private static function setEmplRepresent($arr_vals, $table_name, $list_id, $item_id) {
            try {
                $arr_vals[\App\Http\Controllers\TasksController::REPRESENT_EMPL] = Helper::getMetaValByField($table_name, $list_id, \App\Http\Controllers\TasksController::REPRESENT_EMPL, $item_id);
            }
            catch (\Exception $e) {
                if ($e instanceof Exceptions\DXNoRepresentField) {
                    $arr_vals[\App\Http\Controllers\TasksController::REPRESENT_EMPL] = null;
                }
                else {
                    throw $e;
                }
            } 
            
            return $arr_vals;
        }
        
        /**
         * Atgriež reģistra datu bāzes tabulas nosaukumu
         * 
         * @param integer $list_id Reģistra ID
         * @return string Reģistra datu bāzes tabulas nosaukums
         */
        public static function getListTableName($list_id) {
            $list_row = DB::table("dx_lists")->where("id", "=", $list_id)->first();
            $obj_row = DB::table("dx_objects")->where("id", "=", $list_row->object_id)->first();
            
            return $obj_row->db_name;
        }
        
        /**
         * Pārbauda vai ir/nav prombūtne un darbinieks vai tā aizvietotājs ir aktīvs (strādā uzņēmumā). 
         * Ja ir aizvietotājs, tad rekursīvi atgriež aizvietotāju.
         * 
         * @param integer $empl_id Darbinieka ID
         * @return Array Masīvs ar aizvietošanas/izpildītāja informāciju: employee_id - izpildītāja ID, subst_info - aizvietošanas gadījumā aizvietotāju dati teksta veidā
         */
        public static function getSubstitEmpl($empl_id, $info) {
            $subst = DB::table('dx_users_left')
                     ->where('user_id', '=', $empl_id)
                     ->whereRaw('now() between ifnull(left_from, DATE_ADD(now(), INTERVAL -1 DAY)) and ifnull(left_to, DATE_ADD(now(), INTERVAL 1 DAY))')
                     ->first();
            
            if ($subst) {
                // ir aizvietošana
                $user = DB::table('dx_users')->where('id', '=', $empl_id)->first();
                
                if (strlen($info) > 0) {
                    $info .= " -> ";
                }
                $info .= $user->display_name;
                
                if ($subst->substit_empl_id) {
                    return Helper::getSubstitEmpl($subst->substit_empl_id, $info); // atgriežam aizvietotāju (pārbaudot tā prombūtni)
                }
                else {                    
                    throw new Exceptions\DXCustomException(sprintf(trans('workflow.err_no_substitute'), $user->display_name));
                }
            }
            
            return Helper::validateEmployee($empl_id, $info);           
        }
        
        /**
         * Atgriež ieraksta pašreizējo uzdevumu, kas ir procesā
         * 
         * @param integer $list_id Reģistra ID
         * @param integer $item_id Ieraksta ID
         * @return Object Uzdevuma rindas objekts
         */
        public static function getCurrentTask($list_id, $item_id) {
            return  DB::table("dx_tasks")
                    ->where('list_id', '=', $list_id)
                    ->where('item_id', '=', $item_id)
                    ->where('task_type_id', '!=', \App\Http\Controllers\TasksController::TASK_TYPE_INFO)
                    ->whereNull('task_closed_time')
                    ->whereNull('parent_task_id')
                    ->first();
        }               
        
         /**
         * Pārbauda, vai darbplūsmas solī iestatītais lauks satur informāciju par darbinieku
         * Darbinieki tiek glabāti tabulā dx_users
         * 
         * @param object $fld_row Lauka objekts
         * @throws Exceptions\DXCustomException
         */
        public static function validateEmplField($fld_row) {
            
            if (!$fld_row) {
                throw new Exceptions\DXCustomException("Nekorekti konfigurēta darbplūsma, jo nav norādīts dokumenta darbinieka lauks!");
            }
            
            if (!$fld_row->rel_list_id) {
                throw new Exceptions\DXCustomException("Nekorekti konfigurēta darbplūsma, jo dokumenta lauks '" . $fld_row->title_form . "' nesatur informāciju par darbinieku!");
            }
            
            $user_table = \App\Libraries\Workflows\Helper::getListTableName($fld_row->rel_list_id);
            
            if ($user_table != 'dx_users') {
                throw new Exceptions\DXCustomException("Nekorekti konfigurēta darbplūsma, jo dokumenta lauks '" . $fld_row->title_form . "' nesatur informāciju par darbinieku!");
            }            
        }
        
        /**
         * Pārbauda, vai darbplūsmas solī iestatītais datuma lauks satur informāciju par termiņu
         * 
         * @param object $fld_row Lauka objekts
         * @throws Exceptions\DXCustomException
         */
        public static function validateDueField($fld_row) {
            
            if (!$fld_row) {
                throw new Exceptions\DXCustomException("Nekorekti konfigurēta darbplūsma, jo nav norādīts dokumenta izpildes termiņa lauks!");
            }
            
            if ($fld_row->type_id != \App\Http\Controllers\TasksController::FIELD_TYPE_DATE) {
                throw new Exceptions\DXCustomException("Nekorekti konfigurēta darbplūsma, jo dokumenta lauks '" . $fld_row->title_form . "' nav datums!");
            }           
        }
        
        /**
         * Pārbauda, vai darbplūsmas solī iestatītais rezolūcijas lauks satur informāciju
         * 
         * @param object $fld_row Lauka objekts
         * @throws Exceptions\DXCustomException
         */
        public static function validateResolutionField($fld_row) {
            
            if (!$fld_row) {
                throw new Exceptions\DXCustomException("Nekorekti konfigurēta darbplūsma, jo nav norādīts dokumenta rezolūcijas lauks!");
            }
            
            if (!($fld_row->type_id == \App\Http\Controllers\TasksController::FIELD_TYPE_TEXT || $fld_row->type_id == \App\Http\Controllers\TasksController::FIELD_TYPE_LONG_TEXT)) {
                throw new Exceptions\DXCustomException("Nekorekti konfigurēta darbplūsma, jo dokumenta lauks '" . $fld_row->title_form . "' nav teksts!");
            }           
        }
        
        /**
         * Atgriež  vērtību, kas norādīta dokumenta konkrētajā laukā 
         * 
         * @param integer $list_id Reģistra ID
         * @param integer $item_id Dokumenta ieraksta ID
         * @param object $fld_row Darbinieka lauka objekts
         * @return mixed Vērtība (var būt dažādi datu tipi, atkarīgs no lauka)
         * @throws Exceptions\DXCustomException
         */
        public static function getDocEmplValue($list_id, $item_id, $fld_row) {
            $doc_table = \App\Libraries\Workflows\Helper::getListTableName($list_id);
            
            $doc_data = DB::table($doc_table)
                        ->select($fld_row->db_name . ' as val')
                        ->where('id', '=', $item_id)
                        ->first();
            
            if (!$doc_data) {
                throw new Exceptions\DXCustomException("Nekorekti konfigurēta darblūsma! Nav nosakāma dokumenta lauka '" . $fld_row->title_form . "' vērtība!");
            }
            
            if (!$doc_data->val) {
                throw new Exceptions\DXCustomException("Dokumenta laukā '" . $fld_row->title_form . "' nav norādīta vērtība!");
            }
            
            return $doc_data->val;
        }
        
        /**
        * Pārbauda vai lietotājam uz ierakstu ir izveidots kāds uzdevums
        * Tādā gaījumā, ieraksts jāatver vismaz skatīšanās režīmā
        * 
        * @param integer $list_id Reģista ID
        * @param integer $item_id Ieraksta ID
        * @return boolean True - ja ir kāds uzdevums, False - ja nav neviens uzdevums
        */
       public static function isRelatedTask($list_id, $item_id) {
           $task = DB::table('dx_tasks')
                   ->where('list_id', '=', $list_id)
                   ->where('item_id', '=', $item_id)
                   ->where('task_employee_id', '=', Auth::user()->id)
                   ->first();

           return ($task != null);
        }
        
        /**
         * Izgūst dokumenta lauka vērtību, kura tiks saglabāta pie uzdevuma
         * 
         * @param string    $table_name     Tabulas nosaukums
         * @param integer   $list_id        Reģistra ID
         * @param integer   $fld_represent  Reģistra lauka ID
         * @param integer   $item_id        Ieraksta ID
         * @return string                   Dokumenta lauka vērtība
         * @throws Exceptions\DXCustomException
         */
        private static function getMetaValByField($table_name, $list_id, $fld_represent, $item_id) {
            $fld_row = DB::table('dx_views')
                    ->select(DB::raw("dx_views_fields.field_id"))
                    ->join('dx_views_fields', 'dx_views.id', '=', 'dx_views_fields.view_id')
                    ->where('dx_views.is_for_workflow', '=', 1)
                    ->where('dx_views_fields.represent_id', '=', $fld_represent)
                    ->where('dx_views.list_id', '=', $list_id)
                    ->first();

            if (!$fld_row) {
                throw new Exceptions\DXNoRepresentField();
            }

            $fld_val_row = DB::table("dx_lists_fields")->where("id", "=", $fld_row->field_id)->first();

            $val_row = DB::table($table_name)->select(DB::raw($fld_val_row->db_name . " as val"))->where('id', '=', $item_id)->first();

            if ($val_row->val && ($fld_val_row->type_id == \App\Libraries\DBHelper::FIELD_TYPE_LOOKUP || $fld_val_row->type_id == \App\Libraries\DBHelper::FIELD_TYPE_RELATED || \App\Libraries\DBHelper::FIELD_TYPE_MULTILEVEL)) {
                $obj_row = \App\Libraries\DBHelper::getListObject($fld_val_row->rel_list_id);
                $fld_row = DB::table("dx_lists_fields")->where("id", "=", $fld_val_row->rel_display_field_id)->first();
                
                $val_row = DB::table($obj_row->db_name)->select(DB::raw($fld_row->db_name . " as val"))->where('id', '=', $val_row->val)->first();
            }
            
            return $val_row->val;
        }
       
        /**
         * Pārbauda, vai darbinieks strādā uzņēmumā (sācis vai nav atlaists)
         * 
         * @param integer $empl_id Darbinieka ID
         * @param string $info Informācija par aizvietotājiem
         * @return Array Masīvs ar aizvietošanas/izpildītāja informāciju: employee_id - izpildītāja ID, subst_info - aizvietošanas gadījumā aizvietotāju dati teksta veidā
         * @throws Exceptions\DXCustomException
         */
        public static function validateEmployee($empl_id, $info) {
            // pārbaudam vai jau strādā un vēl nav atbrīvots no darba
            $user = DB::table('dx_users')
                    ->select(
                            'display_name', 
                            DB::raw('ifnull(valid_from, DATE_ADD(now(), INTERVAL -1 DAY)) as valid_from'), 
                            DB::raw('ifnull(valid_to, DATE_ADD(now(), INTERVAL 1 DAY)) as valid_to'),
                            'email',
                            'picture_guid',
                            'position_title'
                            )
                    ->where('id', '=', $empl_id)
                    ->first();
            
            $dat_now = strtotime(date('Y-n-d H:i:s'));
            
            if (!($dat_now >= strtotime($user->valid_from) && $dat_now <= strtotime($user->valid_to))) {
                throw new Exceptions\DXCustomException("Darbinieks '" . $user->display_name . "' nestrādā uzņēmumā!");
            }
            
            if (strlen($info) > 0) {
                $info = $info . " -> " . $user->display_name;
            }
            
            // nav prombūtnē un strādā uzņēmumā
            return array(
                'employee_id' => $empl_id, 
                'subst_info' => $info, 
                'email' => $user->email, 
                'display_name' => $user->display_name,
                'picture_guid' => $user->picture_guid,
                'position_title' => $user->position_title
            );
        }
        
        /**
         * Gets item's workflow cancelation info
         * 
         * @param integer $list_id List ID
         * @param integer $item_id Item ID
         * @return object Data with wf cancelation info
         */
        public static function getWFRejectedInfo($list_id, $item_id) {
   
            //find last noninformative task
            $last_task = DB::table('dx_tasks')
                            ->where('list_id', '=', $list_id)
                            ->where('item_id', '=', $item_id)
                            ->where('task_type_id', '!=', \App\Http\Controllers\TasksController::TASK_TYPE_INFO)
                            ->orderBy('id', 'DESC')
                            ->first();

            // lets findout if workflow was canceled forced
            $wf_info = DB::table('dx_workflows_info as i')
                    ->select('u.display_name', 'i.end_time as task_closed_time', 'u.picture_guid', 'u.id as end_user_id', 'i.comment as task_comment')
                    ->leftJoin('dx_users as u', 'i.init_user_id', '=', 'u.id')
                    ->where('i.id','=',$last_task->wf_info_id)
                    ->where('i.is_forced_end', '=', 1)
                    ->first();

            if ($wf_info) {                
                return $wf_info;
            }
            else {                
                // lets find last task with status rejected row
                return  DB::table('dx_tasks as t')
                                 ->select('u.display_name', 't.task_comment', 't.task_closed_time', 'u.picture_guid', 'u.id as end_user_id')
                                 ->leftJoin('dx_users as u', 't.task_employee_id', '=', 'u.id')
                                 ->where('t.list_id', '=', $list_id)
                                 ->where('t.item_id', '=', $item_id)
                                 ->where('t.task_type_id', '!=', \App\Http\Controllers\TasksController::TASK_TYPE_INFO)
                                 ->where('t.task_status_id', '=', \App\Http\Controllers\TasksController::TASK_STATUS_DENY)
                                 ->orderBy('t.id', 'DESC')
                                 ->first();
            }
        }
        
         /**
        * Nosaka ieraksta apstiprināšanas statusu (ja ir definēta apstiprināšanas darbplūsma)
        * 
        * @param integer $list_id  Reģistra ID
        * @param integer $item_id  Ieraksta ID
        * @return integer  Apstiprināšanas statuss no tabulas dx_item_statuses
        */
        public static function getItemApprovalStatus($list_id, $item_id)
        {
            if ($item_id == 0) {
                return 1; // ieraksts nav apstiprināts, jo vispār vēl nav pat saglabāts
            }

            $doc_table = \App\Libraries\Workflows\Helper::getListTableName($list_id);

            $item_data = DB::table($doc_table)
                         ->select('dx_item_status_id')
                         ->where("id", "=", $item_id)
                         ->first();

            if ($item_data) {
                return ($item_data->dx_item_status_id >0) ? $item_data->dx_item_status_id : 1;
            }

            return 1; // workflow not started
        }

    }

}
<?php

namespace App\Libraries\Structure
{
    use Input;
    use DB;
    use App\Exceptions;
    use Auth;
    use App\Libraries\DBHistory;
    
    /**
     * Darbplūsmas kopēšanas klase
     */
    class StructMethod_wf_copy extends StructMethod
    {        

        /**
         * Kopējamās darbplūsmas ID
         * 
         * @var integer
         */
        private $wf_id = 0;
        
        /**
         * Darbplūsmas tabulas ieraksta objekts
         * 
         * @var object
         */
        private $wf_row = null;
        
        /**
         * Reģistra ID uz kuru tiks kopēta darbplūsma
         * 
         * @var integer
         */
        private $list_id = 0;            
        
        /**
         * Jaunās darbplūsmas nosaukums
         * 
         * @var string
         */
        private $wf_title = "";
        
        /**
         * Integritātes nosacījums - ko darīt kolīziju gadījumā (1 - ziņot par kļūdu, 2 - ignorēt, 0 - nav uzstādīts)
         * @var integer
         */
        private $integrity_id = 0;
        
        /**
         * Inicializē klases parametrus
         * 
         * @return void
         */

        public function initData()
        {
            $this->wf_id = Input::get('wf_id', 0); // ja POST formu uz ģenerēšanu

            $this->list_id = Input::get('item_id', 0); // ja izsauc formas atrādīšanu ar AJAX            
            
            $this->wf_title = Input::get('wf_title', ''); // ja POST formu uz ģenerēšanu
            
            $this->integrity_id = Input::get('integrity_id', 0);
        }

        /**
         * Atgriež darbplūsmas kopēšanas uzstādījumu HTML formu
         * 
         * @return string HTML forma
         */

        public function getFormHTML()
        {            
            $wf_list = $this->getObjTable('dx_workflows_def');
            
            return view('structure.wf_copy', [
                        'form_guid' => $this->form_guid,
                        'wfs' => $this->getWflows(),
                        'list_id' => $this->list_id,
                        'wf_list_id' => $wf_list->list_id
                    ])->render();
        }

        /**
         * Izveido skatu
         * 
         * @return void
         */

        public function doMethod()
        {
            $this->validateData();

            DB::transaction(function ()
            {
                $this->copyWf();
            });
        }

         /**
         * Izgūst visas darbplūsmas
         * 
         * @return Array Masīvs ar darbplūsmām
         */

        private function getWflows()
        {
            $wfs = DB::table('dx_workflows_def')->orderBy('title', 'ASC')->get();

            if (count($wfs) == 0)
            {
                throw new Exceptions\DXCustomException("Nav atrodama neviena darbplūsma, kuru varētu kopēt!");
            }

            return $wfs;
        }
        
        /**
         * Pārbauda, vai norādīti obligātie lauki un tie ir korekti
         * 
         * @return void
         */

        private function validateData()
        {
            if ($this->wf_title == '' || $this->wf_id == 0 || $this->integrity_id == 0)
            {
                throw new Exceptions\DXCustomException("Ir jānorāda jaunās darbplūsmas nosaukums vai integritātes nosacījums vai darbplūsma, no kuras tiks veikta kopēšana!");
            }
            
            $this->wf_row = DB::table('dx_workflows_def')->where('id', '=',$this->wf_id)->first();
            
            if (!$this->wf_row) {
                throw new Exceptions\DXCustomException("Darbplūsma ar ID '" . $this->view_id . "' neeksistē!");
            }
        }

        /**
         * Izveido datu bāzes darbplūsmas ieraksta kopiju
         * 
         * @param Array      $obj_fields    Oriģinālās darbplūsmas masīvs ar laukiem
         * @param string     $title         Jaunās darbplūsmas nosaukums
         * @return integer                  Jaunās darbplūsmas ID
         */

        private function getNewWfID($obj_fields, $title)
        {
            $obj_table = 'dx_workflows_def';
            
            $flds = array();
            foreach ($obj_fields as $key => $val)
            {
                if ($key != "id" && $key != "modified_user_id" && $key != "modified_time")
                {
                    $flds[$key] = $val;
                }
            }
            $flds['title'] = $title;
            $flds['valid_from'] = date('Y-n-d');
            $flds['list_id'] = $this->list_id;
            
            $flds['created_user_id'] = Auth::user()->id;
            $flds['created_time'] = date('Y-n-d H:i:s');
            
            $new_id = DB::table($obj_table)->insertGetId($flds);
            
            $wf_table = $this->getObjTable($obj_table);
            
            $history = new DBHistory($wf_table, null, null, $new_id);
            $history->makeInsertHistory();
            
            return $new_id;
        }

        /**
         * Kopē norādītās darbplūsmas soli
         * 
         * @param            $steps_table           Soļa tabulas objekts, izmantojams vēstures veidošanai
         * @param integer    $new_wf_id             Jaunās darbplūsmas ID
         * @param Array      $step                  Masīvs ar kopējamā soļa lauka atribūtiem
         * @return void
         */

        private function copyStep($steps_table, $new_wf_id,  $step)
        {
            $flds = array();
            foreach ($step as $key => $val)
            {
                if (($key == "field_id" || $key == "due_field_id" || $key == "resolution_field_id") && $val) {
                    // jāsamapo korekti lauku ID atbilstoši jaunajam reģistram
                    $old_fld = DB::table('dx_lists_fields')->where('id', '=', $val)->first();
                    
                    $new_fld = DB::table('dx_lists_fields')->where('db_name', '=', $old_fld->db_name)->where('list_id', '=', $this->list_id)->first();
                    
                    if (!$new_fld) {
                        if ($old_fld->db_name == "dx_item_status_id") {
                            // izveidojam jaunajā reģistrā darbplūsmas statusa lauku
                            $flds[$key] = $this->copyListField($old_fld);
                        }
                        else {                        
                            if ($this->integrity_id == 1) {
                                throw new Exceptions\DXCustomException("Nav iespējams noteikt kopējamās darbplūsmas solī Nr. " . $step->step_nr . " norādītā lauka '" . $old_fld->title_form . "' atbilstību, jo reģistrā, uz kuru tiek kopēts, nav šāda lauka!");
                            }
                            else {
                                return; // ignorējam soli
                            }
                        }
                    }
                    else {                    
                        $flds[$key] = $new_fld->id;
                    }
                }
                else if ($key != "id" && $key != "modified_user_id" && $key != "modified_time")
                {
                    $flds[$key] = $val;
                }
            }
            
            $flds['workflow_def_id'] = $new_wf_id;
            $flds['list_id'] = $this->list_id;
            $flds['created_user_id'] = Auth::user()->id;
            $flds['created_time'] = date('Y-n-d H:i:s');
            
            $step_id = DB::table('dx_workflows')->insertGetId($flds);
            $history = new DBHistory($steps_table, null, null, $step_id);
            $history->makeInsertHistory();
            
            if ($step->task_type_id == \App\Http\Controllers\TasksController::TASK_TYPE_FILL_ACCEPT) {
                // jākopē arī saistītie lauki
                $flds = DB::table('dx_workflows_fields')->where('workflow_id', '=', $step->id)->get();
            
                $flds_table = $this->getObjTable('dx_workflows_fields');

                foreach ($flds as $fld)
                {
                    $this->copyFields($flds_table, $step_id, $fld);
                }
            }
        }
        
        /**
         * Kopē reģistra lauku
         * 
         * @param object $old_fld Kopējamā lauka objekts
         * @return integer Jaunā lauka ID
         */
        private function copyListField($old_fld) {
            $flds = array();
            foreach ($old_fld as $key => $val)
            {
                if ($key != "id" && $key != "modified_user_id" && $key != "modified_time")
                {
                    $flds[$key] = $val;
                }
            }            
            
            $flds['list_id'] = $this->list_id;
            $flds['created_user_id'] = Auth::user()->id;
            $flds['created_time'] = date('Y-n-d H:i:s');
            
            $flds_table = $this->getObjTable('dx_lists_fields');
            $fld_id = DB::table('dx_lists_fields')->insertGetId($flds);
            $history = new DBHistory($flds_table, null, null, $fld_id);
            $history->makeInsertHistory();
            
            return $fld_id;
        }
        
        /**
         * Kopē darbplūsmas soļa laukus
         * 
         * @param object $flds_table Lauka tabulas objekts
         * @param integer $new_step_id Jaunā soļa ID
         * @param object $fld Kopējamā lauka objekts
         * 
         * @throws Exceptions\DXCustomException
         */
        private function copyFields($flds_table, $new_step_id, $fld) {
            $flds = array();
            foreach ($fld as $key => $val)
            {
                if (($key == "field_id") && $val) {
                    // jāsamapo korekti lauku ID atbilstoši jaunajam reģistram
                    $old_fld = DB::table('dx_lists_fields')->where('id', '=', $val)->first();
                    
                    $new_fld = DB::table('dx_lists_fields')->where('db_name', '=', $old_fld->db_name)->where('list_id', '=', $this->list_id)->first();
                    
                    if (!$new_fld) {
                        if ($old_fld->db_name == "dx_item_status_id") {
                            // izveidojam jaunajā reģistrā darbplūsmas statusa lauku
                            $flds[$key] = $this->copyListField($old_fld);
                        }
                        else {
                            if ($this->integrity_id == 1) {
                                throw new Exceptions\DXCustomException("Nav iespējams noteikt kopējamās darbplūsmas izmantotā lauka '" . $old_fld->title_form . "' atbilstību, jo reģistrā, uz kuru tiek kopēts, nav šāda lauka!");
                            }
                            else {
                                return; // ignorējam lauku
                            }
                        }
                    }
                    else {                    
                        $flds[$key] = $new_fld->id;
                    }
                }
                else if ($key != "id" && $key != "modified_user_id" && $key != "modified_time")
                {
                    $flds[$key] = $val;
                }
            }
            
            $flds['workflow_id'] = $new_step_id;
            $flds['list_id'] = $this->list_id;
            $flds['created_user_id'] = Auth::user()->id;
            $flds['created_time'] = date('Y-n-d H:i:s');
            
            $fld_id = DB::table('dx_workflows_fields')->insertGetId($flds);
            $history = new DBHistory($flds_table, null, null, $fld_id);
            $history->makeInsertHistory();
        }

        /**
         * Kopē darbplūsmu un tās soļus
         * 
         * @return void
         */

        private function copyWf()
        {            
            $new_wf_id = $this->getNewWfID($this->wf_row, $this->wf_title);

            $steps = DB::table('dx_workflows')->where('workflow_def_id', '=', $this->wf_id)->get();
            
            $steps_table = $this->getObjTable('dx_workflows');
            
            foreach ($steps as $step)
            {
                $this->copyStep($steps_table, $new_wf_id, $step);
            }
            
        }
    }

}
<?php

namespace App\Libraries\Structure
{

    use Input;
    use DB;
    use App\Exceptions;
    use Log;
    
    class StructMethod_register_copy extends StructMethod
    {
        /**
          *
          * Reģistra kopēšanas klase
          *
          *
          * Objekts nodrošina reģistra kopēšanu no cita reģistra: reģistra lauki, formas, skati
          *
         */

        private $obj_id = 0;
        private $list_id = 0;
        private $register_title = "";

        /**
         * Inicializē klases parametrus
         * 
         * @return void
         */

        public function initData()
        {
            $this->obj_id = Input::get('obj_id', 0);

            if ($this->obj_id == 0)
            {
                $this->obj_id = Input::get('item_id', 0);
            }

            $this->list_id = Input::get('list_id', 0);
            $this->register_title = Input::get('register_title', '');
        }

        /**
         * Atgriež reģistra kopēšanas uzstādījumu HTML formu
         * 
         * @return string HTML forma
         */

        public function getFormHTML()
        {
            return view('structure.register_copy', [
                        'form_guid' => $this->form_guid,
                        'obj_id' => $this->obj_id,
                        'lists' => $this->getLists()
                    ])->render();
        }

        /**
         * Izveido reģistru: laukus, skatu, formu
         * 
         * @return void
         */

        public function doMethod()
        {
            $this->validateData();

            DB::transaction(function ()
            {
                $old_list_row = DB::table('dx_lists')->where('id', '=', $this->list_id)->first();
                
                $obj_row = DB::table('dx_objects')->where('id', '=', $this->obj_id)->first();

                $new_list_id = DB::table('dx_lists')->insertGetId(
                        array('list_title' => $this->register_title, 'object_id' => $this->obj_id, 'group_id' => $old_list_row->group_id)
                );

                $fields = $this->getListFields($new_list_id);

                $this->copyViews($fields, $new_list_id);
                
                $this->copyForms($fields, $new_list_id);
                
                $this->copyWorkflows($fields, $new_list_id);
            });
        }

        /**
         * Pārbauda, vai norādīti obligātie lauki un vai reģistrs ar tādu nosaukumu neeksistē
         * 
         * @return void
         */

        private function validateData()
        {
            if ($this->obj_id == 0 || $this->register_title == '' || $this->list_id == 0)
            {
                throw new Exceptions\DXCustomException("Visi lauki ir obligāti jānorāda!");
            }

            $list_row = DB::table('dx_lists')->where('object_id', '=', $this->obj_id)->where('list_title', '=', $this->register_title)->first();

            if ($list_row)
            {
                throw new Exceptions\DXCustomException("Reģistrs ar nosaukumu '" . $this->register_title . "' jau eksistē!");
            }
        }

        /**
         * Izgūst visus objekta reģistrus
         * 
         * @return Array Masīvs ar objekta reģistriem
         */

        private function getLists()
        {
            $lists = DB::table('dx_lists')->where('object_id', '=', $this->obj_id)->get();

            if (count($lists) == 0)
            {
                throw new Exceptions\DXCustomException("Objektam nav definēts neviens reģistrs, kuru varētu kopēt!");
            }

            return $lists;
        }

        /**
         * Nokopē reģistra laukus un papildina masīvu ar veco/jauno lauku ID atbilstībām
         * 
         * @return Array Masīvs reģistra laukiem
         */

        private function getListFields($new_list_id)
        {
            $fields = DB::table('dx_lists_fields')->where('list_id', '=', $this->list_id)->get();

            if (!$fields)
            {
                throw new Exceptions\DXCustomException("Reģistram, no kura jāveic kopēšana, nav definēts neviens lauks!");
            }

            foreach ($fields as $row)
            {
                $flds = array();
                foreach ($row as $key => $val)
                {
                    if ($key != "id")
                    {
                        $flds[$key] = $val;
                    }
                }

                if ($flds["db_name"] == "list_id" && $flds["list_id"] == $flds["default_value"] && $flds["list_id"] == $flds["criteria"]) {
                    $flds["default_value"] = $new_list_id;
                    $flds["criteria"] = $new_list_id;
                }
                
                $flds['list_id'] = $new_list_id;

                $row->_tmp_id_new = DB::table('dx_lists_fields')->insertGetId($flds);
            }


            return $fields;
        }

        /**
         * Meklē ierakstu lauku masīvā pēc lauka ID
         * 
         * @param Array     $fields  Masīvs ar laukiem, kurā meklēt
         * @param string    $search_id  Meklējamā ieraksta ID vērtība
         * @return integer  Atrastā atbilstība - jaunā ieraksta ID
         */

        private function searchFieldMap($fields, $search_id)
        {
            foreach ($fields as $row)
            {
                if ($row->id == $search_id)
                {
                    return $row->_tmp_id_new;
                }
            }

            throw new Exceptions\DXCustomException("Sistēmas kļūda - nav iespējams noteikt kopējamo reģistra lauku atbilstību!");
        }

        /**
         * Izveido datu bāzes objekta kopiju
         * 
         * @param string     $obj_table     Objekta datu bāzes tabulas nosaukums
         * @param Array      $obj_fields    Oriģinālā objekta masīvs ar laukiem
         * @param integer    $new_list_id   Jaunā reģistra ID
         * @return integer                   Jaunā objekta ID
         */

        private function getNewObjectID($obj_table, $obj_fields, $new_list_id)
        {
            $flds = array();
            foreach ($obj_fields as $key => $val)
            {
                if ($key != "id" && $key != "list_id")
                {
                    $flds[$key] = $val;
                }
            }
            $flds['list_id'] = $new_list_id;

            return DB::table($obj_table)->insertGetId($flds);
        }

        /**
         * Kopē norādītā objekta laukus
         * 
         * @param string     $obj_table             Objekta tabulas nosaukums
         * @param string     $obj_rel_field_name    Objekta relācijas lauka nosaukums
         * @param Array      $fields                Oriģinālā reģistra lauku masīvs - tiks izmantots, lai iegūtu vecā/jaunā lauka atbilstību
         * @param integer    $new_obj_id            Jaunā objekta ID
         * @param integer    $new_list_id           Jaunā reģistra ID
         * @param Array      $o_field               Masīvs ar kopējamā objekta laukiem
         * @return void
         */

        private function copyObjectFields($obj_table, $obj_rel_field_name, $fields, $new_obj_id, $new_list_id, $o_field)
        {
            $flds = array();
            foreach ($o_field as $key => $val)
            {
                if ($key != "id" && $key != $obj_rel_field_name && $key != "list_id" && $key != "field_id")
                {
                    $flds[$key] = $val;
                }

                if ($key == "field_id" && $val > 0)
                {
                    $flds['field_id'] = $this->searchFieldMap($fields, $val);
                }
            }
            $flds[$obj_rel_field_name] = $new_obj_id;
            $flds['list_id'] = $new_list_id;

            return DB::table($obj_table)->insertGetId($flds);
        }

        /**
         * Kopē visus reģistra skatus
         * 
         * @param Array      $fields        Oriģinālā reģistra lauku masīvs - tiks izmantots, lai iegūtu vecā/jaunā lauka atbilstību
         * @param integer    $new_list_id   Jaunā reģistra ID
         * @return void
         */

        private function copyViews($fields, $new_list_id)
        {
            $views = DB::table('dx_views')->where('list_id', '=', $this->list_id)->get();

            foreach ($views as $view)
            {
                $view->url = null; // nekopēsim url, jo tam jābūt unikālam
                
                if (count($views) == 1 || $view->is_default) {
                    $view->title = $this->register_title; // Ja tikai 1 skats, tad tā nosaukums tāds pats kā jaunajam reģistram
                }
                
                $new_view_id = $this->getNewObjectID('dx_views', $view, $new_list_id);

                $view_fields = DB::table('dx_views_fields')->where('view_id', '=', $view->id)->get();

                foreach ($view_fields as $v_field)
                {
                    $this->copyObjectFields('dx_views_fields', 'view_id', $fields, $new_view_id, $new_list_id, $v_field);
                }
            }
        }

        /**
         * Kopē visas reģistra formas
         * 
         * @param Array      $fields        Oriģinālā reģistra lauku masīvs - tiks izmantots, lai iegūtu vecā/jaunā lauka atbilstību
         * @param integer    $new_list_id   Jaunā reģistra ID
         * @return void
         */

        private function copyForms($fields, $new_list_id)
        {
            $forms = DB::table('dx_forms')->where('list_id', '=', $this->list_id)->get();

            foreach ($forms as $form)
            {
                if (count($forms) == 1) {
                    $form->title = rtrim($this->register_title, "i") . "s"; // pārveidojam vienskaitlī reģistra nosaukumu
                }
                
                $new_form_id = $this->getNewObjectID('dx_forms', $form, $new_list_id);

                $form_fields = DB::table('dx_forms_fields')->where('form_id', '=', $form->id)->get();

                foreach ($form_fields as $f_field)
                {
                    $this->copyObjectFields('dx_forms_fields', 'form_id', $fields, $new_form_id, $new_list_id, $f_field);
                }
            }
        }
        
        /**
         * Kopē darpblūsmas
         * 
         * @param Array      $fields        Oriģinālā reģistra lauku masīvs - tiks izmantots, lai iegūtu vecā/jaunā lauka atbilstību
         * @param integer    $new_list_id   Jaunā reģistra ID
         */
        private function copyWorkflows($fields, $new_list_id) {
            $wf_defs = DB::table('dx_workflows_def')->where('list_id', '=', $this->list_id)->get();

            foreach ($wf_defs as $wf_def)
            {                
                $new_wf_def_id = $this->getNewObjectID('dx_workflows_def', $wf_def, $new_list_id);

                $wf_steps = DB::table('dx_workflows')->where('workflow_def_id', '=', $wf_def->id)->get();

                foreach ($wf_steps as $step)
                {
                    $step_new_id = $this->copyObjectFields('dx_workflows', 'workflow_def_id', $fields, $new_wf_def_id, $new_list_id, $step);
                    
                    $wf_fields = DB::table('dx_workflows_fields')->where('workflow_id', '=', $step->id)->get();

                    foreach ($wf_fields as $wf_field)
                    {
                        $this->copyObjectFields('dx_workflows_fields', 'workflow_id', $fields, $step_new_id, $new_list_id, $wf_field);
                    }
                }
            }
        }

    }

}
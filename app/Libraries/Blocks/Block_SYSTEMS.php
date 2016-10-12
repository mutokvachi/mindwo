<?php

namespace App\Libraries\Blocks
{

    use App\Exceptions;
    use Config;
    use Input;
    use DB;

    class Block_SYSTEMS extends Block
    {

        /**
          Sistēmu attēlošanas bloks

          Objekts nodrošina darba sistēmu attēlošanas funkcionalitāti
        */
        
        private $systems = null;
        
        private $sources = null;

        /**
         * Izgūst bloka HTML
         * 
         * @return string Bloka HTML
         */

        public function getHTML()
        {
            $warning = 0;

            foreach ($this->systems as $system) {
                if ($system->is_incident) {
                    $warning++;
                }
            }

            return view('blocks.systems', [
                            'sources' => $this->sources,
                            'systems' => $this->systems,
                            'warning' => $warning,
                            'click2call_url' => get_portal_config('CLICK2CALL_URL'),
                            'fixed_phone_part' => get_portal_config('CLICK2CALL_INNER_PHONE'),
                        ])->render();
        }

        /**
         * Izgūst bloka JavaScript
         * 
         * @return string Bloka JavaScript loģika
         */

        public function getJS()
        {
            return "";
        }

        /**
         * Izgūst bloka CSS
         * 
         * @return string Bloka CSS
         */
        public function getCSS()
        {
            return view('elements.employee_css', array('is_advanced_filter' => true))->render();
        }

        /**
         * Izgūst bloka JSON datus
         * 
         * @return string Bloka JSON dati
         */
        public function getJSONData()
        {
            return "";
        }

        /**
         * Izgūst bloka parametra vērtības un izpilda sistēmu izgūšanu masīvā
         * Parametrus norāda lapas HTML teksta veidā speciālos simbolos [[OBJ=SYSTEMS]]
         *
         * @return void
         */
        protected function parseParams()
        {
            $this->systems = $this->getSystems();
            $this->sources = $this->getSources();
            
            // $this->addJSInclude('metronic/global/plugins/cubeportfolio/js/jquery.cubeportfolio.min.js');
            $this->addJSInclude('js/blocks/systems.js');
        }

        /**
         * Izgūst attēlojamo sistēmu masīvu
         * 
         * @return Array Sistēmu masīvs
         */
        private function getSystems()
        {
            $systems = DB::table('in_systems as s')
                       ->select(DB::raw('
                            s.*,

                            emp.employee_name as emp_employee_name,
                            emp.picture_guid as emp_picture_guid,
                            emp.position as emp_position,
                            emp.email as emp_email,
                            emp.phone as emp_phone,
                            emp.office_address as emp_office_address,
                            emp.office_cabinet as emp_office_cabinet,
                            emp.manager_id as emp_manager_id,
                            emp.source_id as emp_source_id,
                            subst.id as emp_substit_id,
                            subst.employee_name as emp_substit_employee_name,
                            es.title as emp_source_title,
                            es.icon_class as emp_source_icon,

                            d.title as emp_department,
                            ds.title as source_title,
                            ds.icon_class as source_icon,
                            man.employee_name as emp_manager_name,
                            le.title as emp_left_reason,
                            case when day(emp.birth_date) = day(now()) and month(emp.birth_date) = month(now()) then 1 else 0 end as emp_is_today,
                            case when now() between emp.left_from and emp.left_to then emp.left_to else null end as emp_left_to_date
                        '))
                        ->leftJoin('in_sources as ds', 's.source_id', '=', 'ds.id')         // Datu avots (sistēma)
                        ->leftJoin('in_employees as emp', 's.employee_id', '=', 'emp.id')   // Darbinieks (sistēmas atbildīgais)
                        ->leftJoin('in_sources as es', 'emp.source_id', '=', 'es.id')       // Datu avots (darbinieks)
                        ->leftJoin('in_employees as subst','emp.substit_empl_id', '=', 'subst.id') // Darbinieka aizvietotājs
                        ->leftJoin('in_employees as man','emp.manager_id', '=', 'man.id')          // Darbinieka tiešais vadītājs
                        ->leftJoin('in_left_reasons as le','emp.left_reason_id', '=', 'le.id')     // Prombūtne
                        ->leftJoin('in_departments as d', 'emp.department_id', '=', 'd.id')        // Struktūrvienība
                        ->orderBy('s.name', 'ASC')
                        ->get();
            
            foreach($systems as $sys)
            {
                $incident = DB::table('in_incidents')->where('system_id','=',$sys->id)->orderBy('id','desc')->first();
                
                $sys->is_incident = 0;
                $sys->info = "Sistēma darbojas";
                if ($incident)
                {
                    if (!$incident->solved_time)
                    {
                        $sys->is_incident = 1;
                        $sys->info = (($incident->details) ? $incident->details : "Sistēmas darbības traucējumi");
                    }
                    else
                    {
                        $to_time = strtotime(date('Y-n-d H:i:s'));
                        $from_time = strtotime($incident->solved_time);
                        $minutes = round(abs($to_time - $from_time) / 60,2);
                        
                        if ($minutes < 120) {
                            $sys->info = "Sistēmas darbība ir atjaunota";
                        }
                    }
                }
                
            }

            return $systems;
        }

        /**
         * Atgriež datu avotu masīvu
         * 
         * @return Array Datu avots masīvs
         */
        private function getSources()
        {
            return DB::table('in_sources')->select(DB::raw('*'))->get();
        }

    }

}
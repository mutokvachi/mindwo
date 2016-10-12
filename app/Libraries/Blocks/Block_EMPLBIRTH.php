<?php

namespace App\Libraries\Blocks
{

    use DB;
    use Input;
    use Config;
    use App\Libraries\Rights;
    
    /**
     *
     * Darbinieku dzimšadas dienu klase
     *
     *
     * Objekts nodrošina darbinieku attēlošanu, kuriem ir dzimšanas dienas
     * Kā parametru var padot datu avota ID - tad atlasīs tikai atbilstošos darbiniekus.
     *
     */
    class Block_EMPLBIRTH extends Block
    {

        /**
         * Uzņēmuma ID, pēc kura veikt datu atlasi
         * 
         * @var integer 
         */
        public $source_id = 0;

        /**
         * Masīvs ar atlasītajiem darbiniekiem
         * 
         * @var Array 
         */
        private $employees = null;

        /**
         * Pazīme, vai rādīt šodienas dzimšanas dienas. Ja nav norādīts neviens cits kritērijs, tad tiek atlasīti šodienas ieraksti
         * 
         * @var integer 
         */
        private $show_this_day = 0;

        /**
         * Dzimšanas dienu skaits šodien
         * 
         * @var type 
         */
        private $empl_cnt_day = 0;

        /**
         * Darbinieku meklēšanas kritērijs (vārds, uzvārds)
         * 
         * @var string 
         */
        private $criteria = '';

        /**
         * Meklēšanas kritērijs pēc struktūrvienības nosaukuma
         * 
         * @var type 
         */
        private $department = '';

        /**
         * Filtrēšanas pēc datuma no
         * 
         * @var type 
         */
        private $date_from = '';

        /**
         * Filtrēšana pēc datuma līdz
         * 
         * @var type 
         */
        private $date_to = '';

        /**
         * Izgūst bloka HTML
         * 
         * @return string Bloka HTML
         */
        public function getHTML()
        {
            $empl_list_rights = Rights::getRightsOnList(Config::get('dx.employee_list_id'));
            $is_list_rights = false;
            if ($empl_list_rights) {
                $is_list_rights = $empl_list_rights->is_edit_rights;
            }
        
            return view('blocks.empl_birth', [
                        'block_guid' => $this->block_guid,
                        'employees' => $this->employees,
                        'avatar' => get_portal_config('EMPLOYEE_AVATAR'),
                        'empl_cnt_day' => $this->empl_cnt_day,
                        'click2call_url' => get_portal_config('CLICK2CALL_URL'),
                        'fixed_phone_part' => get_portal_config('CLICK2CALL_INNER_PHONE'),
                        'criteria' => $this->criteria,
                        'department' => $this->department,
                        'source_id' => $this->source_id,
                        'sources' => DB::table('in_sources')->where('is_for_search', '=', 1)->get(),
                        'date_from' => $this->date_from,
                        'date_to' => $this->date_to,
                        'profile_url' => (($is_list_rights) ? '/employee_profile' : "/")
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
            return view('elements.employee_css', ['is_advanced_filter' => 1])->render();
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
         * Izgūst bloka parametra vērtības un izgūst darbiniekus masīvā atbilstoši norādītajiem kritērijiem no meklēšanas formas
         * Parametrus norāda lapas HTML teksta veidā speciālos simbolos [[OBJ=...]]
         * 
         * @return void
         */
        protected function parseParams()
        {
            $this->source_id = Input::get('source_id', 0);
            $this->criteria = trim(Input::get('criteria', ''));
            $this->department = trim(Input::get('department', ''));
            $this->date_from = check_date(Input::get('date_from', date('Y-m-d')), 'yyyy-mm-dd');
            $this->date_to = check_date(Input::get('date_to', date('Y-m-d')), 'yyyy-mm-dd');

            if ($this->source_id == 0 && $this->criteria == '' && $this->department == '' && $this->date_from == '' && $this->date_to == '') {
                $this->show_this_day = 1;
            }

            $this->employees = $this->getEmployees();

            $this->countEmployees();

            $this->addJSInclude('metronic/global/plugins/moment.min.js');
            $this->addJSInclude('metronic/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js');
            $this->addJSInclude('plugins/tree/jstree.min.js');
            $this->addJSInclude('js/pages/employees_links.js');
            $this->addJSInclude('js/pages/search_tools.js');
            $this->addJSInclude('js/pages/date_range.js');
            $this->addJSInclude('js/blocks/emplbirth.js');
        }

        /**
         * Izgūst darbiniekus, kuriem šajā mēnesī ir dzimšanas diena
         * Atlaistie darbinieki netiek iekļauti
         * 
         * @return Array Darbinieku saraksts
         */
        private function getEmployees()
        {
            return DB::table(Config::get('dx.empl_table') . ' as em')
                            ->select(DB::raw('
                                em.birth_date,
                                em.picture_guid,
                                em.' . Config::get('dx.empl_fields.empl_name') . ' as employee_name,
                                em.' . Config::get('dx.empl_fields.empl_position') . ' as position,
                                em.email,
                                em.source_id,
                                em.phone,
                                em.department_id,
                                em.office_address,
                                em.manager_id,
                                em.office_cabinet,
                                em.left_to,
                                em.substit_empl_id,
                                in_sources.title as source_title, 
                                ifnull(in_sources.feed_color,"#f1f4f6") as feed_color,
                                in_sources.icon_class as source_icon,
                                case when day(em.birth_date) = day(now()) and month(em.birth_date) = month(now()) then 1 else 0 end as is_today,
                                man.' . Config::get('dx.empl_fields.empl_name') . ' as manager_name,
                                le.title as left_reason,
                                case when now() between em.left_from and em.left_to then em.left_to else null end as left_to_date,
                                subst.' . Config::get('dx.empl_fields.empl_name') . ' as subst_empl_name,
                                in_departments.title as department,
                                em.id
                            '))
                            ->leftJoin('in_sources', 'em.source_id', '=', 'in_sources.id')
                            ->leftJoin('in_departments', 'em.department_id', '=', 'in_departments.id')
                            ->leftJoin(Config::get('dx.empl_table') . ' as man', 'em.manager_id', '=', 'man.id')
                            ->leftJoin('in_left_reasons as le', 'em.left_reason_id', '=', 'le.id')
                            ->leftJoin(Config::get('dx.empl_table') . ' as subst', 'em.substit_empl_id', '=', 'subst.id')
                            ->whereNull('em.' . Config::get('dx.empl_fields.empl_end_date'))
                            ->whereNotIn('em.id', Config::get('dx.empl_ignore_ids'))
                            ->where(function($query)
                            {
                                if ($this->source_id > 0) {
                                    $query->where('em.source_id', '=', $this->source_id);
                                }

                                if (strlen($this->department) > 0) {
                                    $query->where('in_departments.title', 'like', '%' . $this->department . '%');
                                }

                                if (strlen($this->criteria) > 0) {
                                    $query->where('em.' . Config::get('dx.empl_fields.empl_name'), 'like', '%' . $this->criteria . '%');
                                }

                                if (strlen($this->date_from) && strlen($this->date_to)) {
                                    //Umanību: SQL injekciju risks tiek novērsts metodē parseParams, kur datumi tiek validēti
                                    $query->whereRaw("DATE(CONCAT(year(now()),'-',month(em.birth_date),'-',day(em.birth_date))) between '" . $this->date_from . "' and '" . $this->date_to . "'");
                                }

                                if ($this->show_this_day) {
                                    $query->whereRaw('day(em.birth_date) = day(now()) and month(em.birth_date) = month(now())');
                                }
                            })
                            ->orderBy(DB::raw('month(em.birth_date)'))
                            ->orderBy(DB::raw('day(em.birth_date)'))
                            ->orderBy('em.' . Config::get('dx.empl_fields.empl_name'))
                            ->get();
        }

        /**
         * Saskaita dzimšanas dienas šodien
         * Uzstāda attiecīgo klases parametru empl_cnt_day
         * 
         * @return void
         */
        private function countEmployees()
        {
            $empl = DB::table(Config::get('dx.empl_table') . ' as em')
                    ->select(DB::raw('
                            em.id
                            '))
                    ->whereNull('em.' . Config::get('dx.empl_fields.empl_end_date'));

            $this->empl_cnt_day = $empl->whereRaw('day(em.birth_date) = day(now()) and month(em.birth_date) = month(now())')->count();
        }

    }

}

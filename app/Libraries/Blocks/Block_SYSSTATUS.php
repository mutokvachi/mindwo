<?php

namespace App\Libraries\Blocks {

    use DB;
    use App\Exceptions;
    use Request;
    use Input;

    /**
     * Sistēmas statusu bloka klase
     */
    class Block_SYSSTATUS extends Block
    {
        /**
         * @var integer Pazīme norāda, kādus datus jāielādē pēc atbilstošā dotu avota
         */
        public $source_id = 0;

        /**
         * @var string Bloka nosaukums, kas tiks rādīts virs bloka
         */
        public $block_title = "Sistēmas pieejamība";

        /**
         * @var array Masīvs ar sistēmam un datiem par tām
         */
        private $sys_statuses = array();

        /**
         * @var array Masīvs ar datiem par konkrēto atvērto sistēmu
         */
        private $system_view_data = array();

        /**
         * @var DateTime Datums un laiks, kad pēdējo reizi modificēts ieraksts 
         */
        private $last_modified = '';

        /**
         * @var integer Stundās, cik ilgi rādīt sistēmas ikonu pēc incidenta atrisināšanas
         */
        private $hours_shown = 2;

        /**
         * Izgūst bloka HTML
         * 
         * @return string Bloka HTML
         */
        public function getHTML()
        {
            /* Ja ir ajax pieprasījumā padots item_id, tad nepieciešams ielādēt sistēmas apskates skatu */
            if (Request::has('item_id')) {
                return view('blocks.sysstatus_view', [
                            'system' => $this->system_view_data
                        ])->render();
            } elseif (count($this->sys_statuses) > 0) {
                return view('blocks.sysstatus', [
                            'id' => 'sysstatus_' . $this->source_id,
                            'block_title' => $this->block_title,
                            'block_guid' => $this->block_guid,
                            'sys_statuses' => $this->sys_statuses,
                            'last_modified' => $this->last_modified
                        ])->render();
            } else {
                return "";
            }
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
            return "";
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
         * Izgūst bloka parametra vērtības
         * Parametrus norāda lapas HTML teksta veidā speciālos simbolos [[OBJ=...|SOURCE=...|TITLE=...|SHOWN_HOURS=...]]
         * 
         * @return void
         */
        protected function parseParams()
        {
            $dat_arr = explode('|', $this->params);

            foreach ($dat_arr as $item) {
                $val_arr = explode('=', $item);

                if ($val_arr[0] == "SOURCE") {
                    $this->source_id = getBlockParamVal($val_arr);
                } elseif ($val_arr[0] == "TITLE") {
                    $this->block_title = str_replace("_", " ", getBlockParamVal($val_arr));
                } elseif ($val_arr[0] == "HOURS_SHOWN") {
                    $this->hours_shown = getBlockParamVal($val_arr);
                } else if (strlen($val_arr[0]) > 0) {
                    throw new Exceptions\DXCustomException("Norādīts blokam neatbilstošs parametra nosaukums (" . $val_arr[0] . ")!");
                }
            }

            /* Ja ir ajax pieprasījumā padots item_id, tad nepieciešams ielādēt sistēmas apskates datus */
            if (Request::has('item_id')) {
                $this->getSystemStatusViewData();
            } else {
                $this->getSysStatuses();
            }
            
            $this->addJSInclude('js/blocks/sysstatus.js');
        }

        /**
         *
         * Iegūst sistēmu statusus
         * 
         * @return void
         */
        private function getSysStatuses()
        {
            /* Iegūst sistēmas statusus 
             * 
             * Ja ir neatrisināts incidents tad atgriežam atrisināšanas laiku kā null,
             * pretējā gadījumā atgriežām lielāko atrisināšanas laiku:
             * i.solved_time IS NULL will be evaluated either to 0, when solved_time is not null, or to 1 when it is null
             * MAX(i.solved_time IS NULL) will be evaluated to 0 if there are no solved_time with null values, otherwise its value will be 1.
             * when MAX(i.solved_time IS NULL)=0 it means that there are no rows where solved_time is null, and we need to return MAX(solved_time) in that case, otherwise we need to return NULL.
             */
            $this->sys_statuses = DB::table('in_systems AS s')
                    ->leftJoin('in_incidents AS i', 'i.system_id', '=', 's.id')
                    ->select(DB::raw('s.id, MAX(s.name) AS name, '
                                    . 'case when MAX(i.solved_time IS NULL)=0 THEN MAX(i.solved_time) END AS solved_time, '
                                    . 'MAX(i.modified_time) AS modified_time'))
                    ->whereRaw('(i.solved_time + INTERVAL ' . $this->hours_shown . ' hour >= NOW() OR i.solved_time IS NULL)')
                    ->where(function ($query) {
                        if ($this->source_id != 0) {
                            $query->where('s.source_id', $this->source_id);
                        }
                    })
                    ->groupBy('s.id')
                    ->get();

            // Iegūst pēdējo modificēšanas datumu
            foreach ($this->sys_statuses as $sys) {
                if ($this->last_modified < $sys->modified_time) {
                    $this->last_modified = $sys->modified_time;
                }
            }
        }

        /**
         *
         * Iegūst sistēmu statusa skata datus
         * 
         * @return void
         */
        private function getSystemStatusViewData()
        {
            /* Iegūst sistēmas skata datus */
            $this->system_view_data = DB::table('in_systems AS s')
                    ->leftJoin('in_incidents AS i', 'i.system_id', '=', 's.id')
                    ->leftJoin('in_employees AS e', 'e.id', '=', 's.employee_id')
                    ->select(DB::raw('s.id, s.name, s.url, s.picture_name, s.picture_guid, '
                                    . 'CONCAT_WS(\', \', e.employee_name, e.phone, e.mobile) employee, '
                                    . 'e.email employee_email, '
                                    . 'IFNULL(i.created_time, \'-\') AS created_time, '
                                    . 'IFNULL(i.solved_time, \'-\') AS solved_time, i.details'))
                    ->where('s.id', Input::get('item_id', -1))
                    ->orderBy(DB::raw('ISNULL(i.solved_time)'), 'desc')
                    ->orderBy(DB::raw('i.solved_time'), 'desc')
                    ->first();
        }
    }
}
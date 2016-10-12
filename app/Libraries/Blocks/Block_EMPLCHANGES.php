<?php

namespace App\Libraries\Blocks
{

    use DB;
    use App\Exceptions;
    use Input;

    /**
     *
     * Darbinieku izmaiņu attēlošanas klase
     *
     *
     * Objekts nodrošina darbinieku izmaiņu attēlošanu
     *
     */
    class Block_EMPLCHANGES extends Block
    {

        public $source_id = 0;
        private $changes_items = null;
        private $row_limit = 10;
        private $criteria = "";
        private $date_from = "";
        private $date_to = "";
        private $is_search = 1;
        private $rows_count = 0;
        private $is_new = 0;
        private $is_change = 0;
        private $is_leave = 0;

        /**
         * Izgūst bloka HTML
         * 
         * @return string Bloka HTML
         */
        public function getHTML()
        {
            $picker_from_html = get_htm_for_datetime_field('search_form', 'date_from', Input::get('date_from', ''));
            $picker_to_html = get_htm_for_datetime_field('search_form', 'date_to', Input::get('date_to', ''));

            return view('blocks.empl_changes', [
                        'block_guid' => $this->block_guid,
                        'changes_items' => $this->changes_items,
                        'criteria' => Input::get('criteria', ''),
                        'picker_from_html' => $picker_from_html,
                        'picker_to_html' => $picker_to_html,
                        'is_search' => $this->is_search,
                        'date_from' => $this->date_from,
                        'date_to' => $this->date_to,
                        'rows_count' => $this->rows_count,
                        'is_new' => $this->is_new,
                        'is_change' => $this->is_change,
                        'is_leave' => $this->is_leave,
                        'avatar' => get_portal_config('EMPLOYEE_AVATAR'),
                        'sources' => DB::table('in_sources')->where('is_for_search', '=', 1)->get(),
                        'source_id' => $this->source_id
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
            return view('blocks.empl_changes_css')->render();
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
         * Izgūst bloka parametra vērtības un izpilda aktīvo ziņu izgūšanu masīvā
         * Parametrus norāda lapas HTML teksta veidā speciālos simbolos [[OBJ=...|SOURCE=...|ROWS=...]]
         * 
         * @return void
         */
        protected function parseParams()
        {
            $dat_arr = explode('|', $this->params);

            foreach ($dat_arr as $item) {
                $val_arr = explode('=', $item);

                if ($val_arr[0] == "SOURCE") {
                    //$this->source_id = getBlockParamVal($val_arr); // Ignorējam, jo tagad šo bloku paredzēts lietot tikai 1 lapā visiem uzņēmumiem kopīgā
                }
                else if ($val_arr[0] == "ROWS") {
                    $this->row_limit = getBlockParamVal($val_arr);
                }
                else if ($val_arr[0] == "SEARCH") {
                    $this->is_search = getBlockParamVal($val_arr);
                }
                else if (strlen($val_arr[0]) > 0) {
                    throw new Exceptions\DXCustomException("Norādīts blokam neatbilstošs parametra nosaukums (" . $val_arr[0] . ")!");
                }
            }

            $this->is_new = Input::get('ch_new', 0);
            $this->is_change = Input::get('ch_change', 0);
            $this->is_leave = Input::get('ch_leave', 0);

            $this->criteria = Input::get('criteria', '');

            $this->date_from = Input::get('date_from', '');
            $this->date_to = Input::get('date_to', '');
            
            $this->source_id = Input::get('source_id', 0);

            $this->changes_items = $this->getChanges();

            
            $this->addJSInclude('metronic/global/plugins/moment.min.js');
            $this->addJSInclude('metronic/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js');
            $this->addJSInclude('js/pages/date_range.js');
            $this->addJSInclude('js/blocks/emplchanges.js');
        }

        /**
         * Izgūst darbinieku izmaiņas
         * 
         * @return Array Darbinieku izmaiņas
         */
        private function getChanges()
        {
            $changes = DB::table('in_employees_history')
                    ->select(DB::raw('in_employees_history.*, os.title as old_source_title, ns.title as new_source_title, in_employees.employee_name, in_employees.email, in_employees.picture_guid'))
                    ->leftJoin('in_sources as os', 'in_employees_history.old_source_id', '=', 'os.id')
                    ->leftJoin('in_sources as ns', 'in_employees_history.new_source_id', '=', 'ns.id')
                    ->leftJoin('in_employees', 'in_employees_history.employee_id', '=', 'in_employees.id')
                    ->where(function($query)
            {

                if ($this->source_id > 0) {
                    $query = $query->where(function($query_or)
                    {
                        $query_or->where('in_employees_history.old_source_id', '=', $this->source_id)
                                 ->orWhere('in_employees_history.new_source_id', '=', $this->source_id);
                    });
                }

                if (strlen($this->criteria) > 0) {
                    $query = $query->where(function($query_or)
                    {

                        $query_or->where('in_employees.employee_name', 'like', '%' . $this->criteria . '%')
                        ->orWhere('in_employees_history.old_department', 'LIKE', '%' . $this->criteria . '%')
                        ->orWhere('in_employees_history.new_department', 'LIKE', '%' . $this->criteria . '%')
                        ->orWhere('in_employees_history.old_position', 'LIKE', '%' . $this->criteria . '%')
                        ->orWhere('in_employees_history.new_position', 'LIKE', '%' . $this->criteria . '%');
                    });
                }

                if (strlen($this->date_from) > 0 && strlen($this->date_to) > 0) {
                    $query = $query->whereDate('in_employees_history.valid_from', '>=', $this->date_from)
                            ->whereDate('in_employees_history.valid_from', '<=', $this->date_to);
                }

                $query = $query->where(function($query_or)
                {

                    if ($this->is_new) {
                        $query_or = $query_or->where(function($q_new)
                        {
                            $q_new->whereNotNull('in_employees_history.new_source_id')
                            ->whereNull('in_employees_history.old_source_id');
                        });
                    }

                    if ($this->is_leave) {
                        $query_or = $query_or->orWhere(function($q_new)
                        {
                            $q_new->whereNotNull('in_employees_history.old_source_id')
                            ->whereNull('in_employees_history.new_source_id');
                        });
                    }

                    if ($this->is_change) {
                        $query_or = $query_or->orWhere(function($q_new)
                        {
                            $q_new->whereNotNull('in_employees_history.old_source_id')
                            ->whereNotNull('in_employees_history.new_source_id');
                        });
                    }
                });
            });


            $this->rows_count = $changes->count();

            return $changes->orderBy('in_employees_history.valid_from', 'DESC')
                            ->orderBy('in_employees.employee_name')
                            ->paginate($this->row_limit);
        }

    }

}

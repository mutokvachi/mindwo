<?php

namespace App\Libraries\Blocks
{

    use Webpatser\Uuid\Uuid;
    use Request;
    use App\Exceptions;
    use App\Libraries\Rights;
    use App\Libraries\DataView;
    use DB;
    use Input;
    use Config;
    use Auth;
    
    /**
     *
     * Grida (tabulas veida saraksta) bloka klase
     *
     *
     * Objekts nodrošina datu attēlošanu gridā (tabulas veida sarakstā)
     *
     */
    class Block_VIEW extends Block
    {

        public $view_id = "";
        public $is_new_button = 0;
        public $grid = null;
        public $rights_htm = "";
        public $grid_title = "";
        
        /**
         * Is rights to import data
         * @var boolean 
         */
        public $is_import_rights = 0;
        
        /**
         * Is rights to customize views
         * @var boolean
         */
        public $is_view_rights = 0;
        
        /**
         * Is view opened in full width/height to fill all page
         * @var boolean 
         */
        public $is_full_page = 0;
        
        // Parametri kas nepieciešami TAB gridam (AJAX izsaukumam)
        public $rel_field_id = 0;
        public $rel_field_value = 0;
        public $form_htm_id = "";
        public $tab_id = "";
        public $tab_prefix = "";
        
        /**
         * This view data (row from table dx_views)
         * @var Array
         */
        public $view_row = null;
        
        /**
         * Izgūst bloka HTML
         * 
         * @return string Bloka HTML
         */
        public function getHTML()
        {
            if (strlen($this->rights_htm) !== 0) {
                return ($this->is_full_page) ? $this->rights_htm : ""; // nav tiesību
            }
            
            return view('blocks.view.view', [
                        'block_id' => $this->grid->grid_data_htm_id,
                        'grid_title' => $this->grid->grid_title,
                        'grid_id' => $this->grid->grid_id,
                        'view_id' => $this->grid->view_id,
                        'list_id' => $this->grid->list_id,
                        'grid_is_paginator' => $this->grid->grid_is_paginator,
                        'filter_data' => $this->grid->filter_data,
                        'show_new_button' => $this->is_new_button,
                        'rel_field_id' => $this->rel_field_id, // tab gridam
                        'rel_field_value' => $this->rel_field_value, // tab gridam
                        'form_htm_id' => $this->form_htm_id, // tab gridam
                        'combo_items' => $this->getViewsComboItems($this->grid->list_id, $this->tab_id, false),
                        'combo_items_my' => $this->getViewsComboItems($this->grid->list_id, $this->tab_id, true),
                        'grid_data_htm_id' => $this->grid->grid_data_htm_id, // tab gridam
                        'menu_id' => $this->grid->menu_id,
                        'tab_id' => $this->tab_id, // tab gridam
                        'tab_prefix' => $this->tab_prefix, // tab gridam 
                        'grid_htm' => $this->grid->getViewHtml(),
                        'paginator_htm' => $this->grid->getPaginatorHtml(),
                        'grid_form' => $this->grid->form_url,
                        'hint' => $this->getListHint($this->grid->list_id),
                        'is_setting_rights' => Rights::isSettingRights(),
                        'open_item_id' => Input::get('open_item_id', 0),
                        'form_type_id' => $this->grid->form_type_id,
                        'operations' => DB::table('dx_field_operations')->orderBy('title')->get(),
                        'view_row' => $this->view_row,
                        'form_id' => $this->grid->form_id,
                        'is_import_rights' => $this->is_import_rights,
                        'is_view_rights' => $this->is_view_rights,
                        'is_full_page' => $this->is_full_page
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
            return view('pages.view_css_includes')->render();
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
         * Parametrus norāda lapas HTML teksta veidā speciālos simbolos [OBJ=...|VIEW_ID=...]
         * 
         * @return void
         */
        protected function parseParams()
        {
            $dat_arr = explode('|', $this->params);

            foreach ($dat_arr as $item) {
                $val_arr = explode('=', $item);

                if ($val_arr[0] == "VIEW_ID") {
                    $this->view_id = getBlockParamVal($val_arr);
                }
                else if ($val_arr[0] == "IS_FULLPAGE") {
                    $this->is_full_page = getBlockParamVal($val_arr);
                }
                else if (strlen($val_arr[0]) > 0) {
                    throw new Exceptions\DXCustomException("Norādīts blokam neatbilstošs parametra nosaukums (" . $val_arr[0] . ")!");
                }
            }

            $this->view_row = $this->getViewRow($this->view_id);

            $this->rights_htm = $this->getRights();

            if (strlen($this->rights_htm) === 0) {
                $this->grid = $this->getGrid();
                $this->grid_title = $this->grid->grid_title;
            }

            $this->fillIncludesArr();
        }

        /**
         * Izgūst reģistra paskaidrojošo tekstu
         * 
         * @param integer $list_id Reģistra ID
         * @return string Paskaidrojošais steksts
         */
        private function getListHint($list_id)
        {
            return DB::table('dx_lists')->where('id', '=', $list_id)->first()->hint;
        }

        /**
         * Aizpilda masīvu ar JavaScript iekļāvumiem
         */
        private function fillIncludesArr()
        {

            if (Request::ajax()) {
                return;
            }
            
            $this->addJSInclude(elixir('js/elix_view.js'));
            
            // Teksta redaktora komponente
            $this->addJSInclude('plugins/tinymce/tinymce.min.js');          
            
        }

        /**
         * Izgūst skata rindas objektu
         * Bloka parametrā skta identifikators var būt gan skaitlis (id vērtība no dx_views tabulas), gan teksts (url vērtība no dx_views tabulas)
         * 
         * @param  mixed  $view_id   Skata identifikators
         * @return Object            Skata rinda
         */
        private function getViewRow($view_id)
        {
            try {
                return getViewRowByID($view_id, $view_id);
            }
            catch (\Exception $ex) {
                throw new Exceptions\DXCustomException("Nekorekts attēlojamā objekta parametrs! Norādīts neeksistējošs VIEW_ID (" . $view_id . ")!");
            }
        }

        /**
         * Pārbauda, vai ir tiesības uz reģistru
         *
         * @return string  Ja nav tiesību, tad atbilstošs paziņojums HTML formātā, ja ir tiesības tad tukšums
         */
        private function getRights()
        {
            // Pārbaudam vai ir tiesības uz reģistru
            $right = Rights::getRightsOnList($this->view_row->list_id);

            if ($right == null) {                
                if (\Request::route()->getName() === "view" && !Config::get("dx.is_all_login_required") && Auth::user()->id == Config::get("dx.public_user_id")) {
                    throw new Exceptions\DXViewAccessException;
                }
                else {
                    return view('elements.error', [
                        'page_title' => trans('errors.access_denied_title'),
                        'message' => sprintf(trans('errors.access_denied_msg'), $this->view_row->title)
                    ])->render();
                }
            }
            else {
                $this->is_new_button = $right->is_new_rights;
                $this->is_import_rights = $right->is_import_rights;
                $this->is_view_rights = $right->is_view_rights;
            }           
             
            return "";
        }

        /**
         * Uzstāda reģistra objektu
         * 
         * @return Object Reģistra objekts
         */
        private function getGrid()
        {
            $grid = DataView\DataViewFactory::build_view('Grid', $this->view_row->id);

            $grid->grid_data_htm_id = 'block_' . Uuid::generate(4);
            
            return $grid;
        }
        
        /**
         * Izgūst reģistra skatus attēlošanai izkrītošajā izvēlnē
         * 
         * @param integer $list_id Reģistra idnetifikators no tabulas dx_lists
         * @param string $tab_id Paneļa HTML id, kurā iekļauts apakšgrids (formas gadījumā)
         * @param boolean $is_my Pazīme, vai attēlot tikai personīgos skatus
         * @return Array Masīvs ar skatu informāciju
         */
        private function getViewsComboItems($list_id, $tab_id, $is_my)
        {
            if ($this->view_row->is_report) {
                return null; // report does not need dropdown with views
            }
            
            $fld_is_hidden = 'is_hidden_from_main_grid';

            if (strlen($tab_id) > 0)
            {
                $fld_is_hidden = 'is_hidden_from_tabs';
            }

            return DB::table('dx_views')
                   ->where('list_id', '=', $list_id)
                   ->whereIn('view_type_id', [1, 9])
                   ->where($fld_is_hidden, '=', 0)
                   ->where(function($query) use ($is_my) {
                       if ($is_my) {
                           $query->where('me_user_id', '=', Auth::user()->id);
                       }
                       else {
                        $query->whereNull('me_user_id');
                       }
                   })
                   ->where('is_report', '=', 0)
                   ->where('is_for_lookup', '=', 0)
                   ->orderBy('title')
                   ->get();
        }

    }

}
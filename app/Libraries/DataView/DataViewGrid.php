<?php
namespace App\Libraries\DataView {
    
    use Webpatser\Uuid\Uuid;
    use DB;
    use Config;
    use Request;
    use App\Exceptions;
    use PDO;
    use Log;
    
    /**
    *
    * Tabulāra saraksta klase
    *
    *
    * Objekts nodrošina datu attēlošanu tabulāra saraksta veidā ar filtrēšanas/kārtošanas iespējām pēc kolonnām
    *
    */
    class DataViewGrid extends DataView {
        
        public $form_url = "";
        
        /**
         * Grid form type - id from table dx_forms_types
         * @var integer
         */
        public $form_type_id = 0;
        
        /**
         * CMS form's ID (from table dx_forms)
         * @var integer 
         */
        public $form_id = 0;
        
        /**
         * Existing employee profile page URL (for view or edit)
         * @var string 
         */
        public $profile_url = '';
        
        public $menu_id = "";
        public $grid_id = "";
        public $grid_title = "";
        public $sorting_direction = 0; // 0 - no sorting; 1 - asc; 2 - desc
        public $sorting_field = "";
        public $grid_rows_in_page = 0;
        public $grid_page_nr = 1;
        public $grid_total_rows = 0;
        public $grid_total_pages = 0;
        
        public $grid_data_htm_id = "";// šis parametrs ir jāuzstāda pirms getViewHtml() izsaukšanas, tajā norāda HTML elementa ID, kurā tiks ielādēts tabulārais saraksts
        
        public $form_htm_id = ""; // šis parametrs ir priekš formu sadaļu gridiem
        
        public $grid_is_paginator = 0; 
        public $filter_data = "";
        
        /**
         * SQL part for SUM fields 
         * @var type 
         */
        public $sql_sum_fld = "";
        
        /**
         * HTML elements helper class (to avoid Blade views render performance issue)
         * 
         * @var App\Libraries\DataView\Helper
         */
        private $helper = null;
        
        /**
        * Inicializē tabulāra saraksta klases objektu.
        * 
        * @param int $view_id Skata identifikatrs (no tabulas dx_views lauks id)
        * @param mixed $filter_data JSON formātā filtrēšanas lauku vērtības
        * @param string $session_guid Unikāla guid vērtība vai arī tukšums. SQL objekti tiek glabāti sesijā, lai uzlabotu ātrdarbību
        * @return void
        */        
        public function __construct($view_id, $filter_data, $session_guid)
        {
            $this->helper = new Helper();
            $this->initObjects($view_id, $filter_data, $session_guid, 0);
            
            $this->setGridFormURL();
            $this->grid_id = $this->view->session_guid;
            $this->grid_title = $this->view->view_obj->grid_title;
            $this->menu_id = Uuid::generate(4);
            $this->filter_data = $filter_data;
            $this->form_htm_id = Request::input('form_htm_id','');
        }
        
        /**
        * Atgriež saraksta datus HTML formātā - responsīva tabula
        * Atgriež kolonnu virsrakstus <thead> un datu rindas <tbody>
        * 
        * @return string saraksta datu HTML
        */
        public function getViewHtml() 
        {  
            $data_htm = $this->getDataRows();
            $head_htm = $this->getHeadingsRow(); // šo vienmēr pēc getDataRows() jāizsauc, lai tiktu uzstādīta sorting_direction vērtība
            $sum_htm = $this->getSumRow(); // šo vienmēr pēc getDataRows() jāizsauc, lai tiktu uzstādīta sorting_direction vērtība
            
            return  view('grid.table', [
                         'grid_id' => $this->grid_id,                         
                         'table_body' => $data_htm . $sum_htm,
                         'table_head' => $head_htm, 
                         'data_attr' => $this->getHTMLDataAttributes(),
                         'filter_data' => $this->filter_data
                    ])->render();
        }
        
        /**
        * Atgriež ierakstu skaitu un lapošanas pogas (HTML), ja ierakstu skaits pārsniedz 1 lapas limitu
        * 
        * @return string ierakstu skaita un lapošanas pogu HTML
        */
        public function getPaginatorHtml()
        { 
            $start_row = $this->grid_rows_in_page * ($this->grid_page_nr - 1) + 1;		

            $end_row = 0;
            if ($this->grid_page_nr != $this->grid_total_pages)
            {
                    $end_row = $this->grid_page_nr * $this->grid_rows_in_page;
            }
            else
            {
                    $end_row = $this->grid_total_rows;// - $start_row + 1;
            }
                        
            $prev_page = 1;
            $next_page = 1;
            
            if (!($this->grid_total_rows <= $this->grid_rows_in_page && $this->grid_page_nr == 1))
            {
                    $prev_page = 1;
                    $next_page = $this->grid_total_pages;

                    if ($this->grid_page_nr-1 > 1)
                    {
                            $prev_page = $this->grid_page_nr-1;
                    }

                    if ($this->grid_page_nr+1 < $this->grid_total_pages)
                    {
                            $next_page = $this->grid_page_nr+1;
                    }

                    $this->grid_is_paginator = 1;
            }

            return  view('grid.records_count', [
                        'grid_id' => $this->grid_id,
                        'is_paginator' => $this->grid_is_paginator,
                        'prev_page' => $prev_page,
                        'grid_page_nr' => $this->grid_page_nr,
                        'grid_total_pages' => $this->grid_total_pages,
                        'next_page' => $next_page,
                        'total_count' => $this->grid_total_rows,
                        'start_row' => $start_row,
                        'end_row' => $end_row,
                        'view_row' => $this->view_row
                    ])->render();
        }
        
        /**
        * Funkcijā tiek definēta SQL daļa tabulārā saraksta datu kārtošanai
        * Tabulārajam sarakstam saskarnē var norādīt kārtošanu pēc kolonnas (uzklikšķinot uz kolonnas notiek AJAX pieprasījums)
        * Šī funkcija ir "protected" - tā tiek izpildīta vecāka objektā DataView, kad tiek konkatinēts izpildāmais datu atslases SQL
        *
        * @return string SQL kārtošanas fragments (ORDER BY)
        */
        protected function getSortingSQL()
        {         
            $this->sorting_direction = Request::input('sorting_direction',0);
            $this->sorting_field = Request::input('sorting_field','');
                        
            $sql_order = "";
            if (strlen($this->view->view_obj->sql_orderby) > 0)
            {
                $sql_order = " ORDER BY " . $this->view->view_obj->sql_orderby;
            }
            
            if ($this->sorting_direction > 0 && strlen($this->sorting_field) > 0)
            {
                // Saskarnē bija noklikšķināta kolonna pēc kuras jāveic kārtošana
                $sql_order = " ORDER BY " . $this->sorting_field;

                if ($this->sorting_direction == 2)
                {
                    $sql_order .= " DESC";
                }
            }
            
            if (strlen($sql_order) > 0)
            {
                // Jāpievieno kārtošana pēc ID, lai apietu MySQL kļūdu, kas ietekmē rezultātu lapošanu (skatīt: http://bugs.mysql.com/bug.php?id=69732)
                $sql_order = $sql_order . ", id DESC";
            }
           
            return $sql_order;
        }
        
        /**
        * Funkcijā tiek definēta SQL daļa datu porcijas lielumam (ierakstu skaits) vienā lapā
        * Šī funkcija ir "protected" - tā tiek izpildīta vecāka objektā DataView, kad tiek konkatinēts izpildāmais datu atslases SQL
        * 
        * @return string SQL ierakstu porcijas fragments (LIMIT)
        */
        protected function getLimitSQL() 
        {
            $this->grid_rows_in_page = Request::input('grid_rows_in_page', Config::get('dx.grid_page_rows_count'));
            $this->grid_page_nr = Request::input('page_nr', 1);
                        
            // dati tiek gatavoti attēlošanai sarakstā, tāpēc ir lapošanas loģika un datu attēlošana pa porcijām
            $limit_start = $this->grid_rows_in_page * ($this->grid_page_nr - 1);               

            return " LIMIT " . $limit_start . ", " . $this->grid_rows_in_page;
        }
        
        /**
        * Funkcija nosaka tabulārā saraksta kopējo ierakstu skaitu un uzstāda atbilstošās vērtības klasei (kopējais ierakstu skaits un lapu kopējais skaits)
        * 
        * @param Array Masīvs ar attēlojamajiem datiem
        * @return string SQL ierakstu porcijas fragments (LIMIT)
        */
        private function setGridTotalRowsCount($rows)
        {
            if (count($rows) < $this->grid_rows_in_page && $this->grid_page_nr == 1)
            {
                // No pagination needed, only 1 page returned
                $this->grid_total_rows = count($rows);
                $this->grid_total_pages = 1;
            }
            else
            {
                $sql_count = $this->view->view_sql . " " . $this->filter_obj->sql;
                
                $sql_count = preg_replace('/SELECT (.*?) FROM/i', 'SELECT count(*) as cnt FROM ', $sql_count, 1);
                                
                $cnt = DB::select($sql_count, $this->filter_obj->arr_filt);

                if (count($cnt) > 0)
                {
                    $cnt_row = $cnt[0];

                    $this->grid_total_rows = $cnt_row->cnt;
                    $this->grid_total_pages = ceil($this->grid_total_rows / $this->grid_rows_in_page);
                }

                if ($this->grid_total_rows == 0)
                {
                    throw new Exceptions\DXCustomException("Nekorekts saraksta SQL - nav iespējams noteikt ierakstu skaitu!");
                }
            }
        }
        
        /**
        * Funkcija izveido HTML tabulu datu rindām - tags <tbody>
        * 
        * @return string HTML ar datu rindām (tags <tbody>)
        */ 
        private function getDataRows()
        {
            $rows = $this->getViewDataArray();
            $view = $this->view->view_obj;
            
            $this->setGridTotalRowsCount($rows);
            
            $htm = "";
            
            foreach($rows as $key => $row)
            {   
                if (!$this->view_row->is_report) {
                    $dropup = "";
                    if ($key > 1)
                    {
                        $dropup = "dropup";
                    }

                    $cell_htm = $this->helper->getBtnsCol(['dropup' => $dropup, 'item_id' => $row['id'], 'form_type_id' => $this->form_type_id]);
                }
                else {
                    $cell_htm = "";
                }
                
                for ($i=0; $i<count($view->model);$i++)
                {
                    if ($this->isFieldIncludable($view->model[$i]))
                    {
                        $cell_obj = Formatters\FormatFactory::build_field($view->model[$i], $row);
                        
                        $cell_obj = $this->formatLinkValue($cell_obj, $view->model[$i], $row);

                        $cell_htm .= $this->helper->getCell([
                            'align' => $cell_obj->align, 
                            'cell_value' => $cell_obj->value,
                            'is_val_html' => $cell_obj->is_html
                        ]);
                    }
                }

                $htm .= $this->helper->getRow(['htm' => $cell_htm]);
            }           
            
            return $htm;
        }
        
        /**
        * Funkcija izveido HTML ar saraksta tabulas kolonnu virsrakstiem - tagi <th>
        * 
        * @return string HTML ar kolonnu virsrakstiem (sakonkatinēti tagi <th>)
        */         
        private function getHeadingsRow()
        {
            $view = $this->view->view_obj;
            $htm_head = ""; // virsraksti
            $htm_flt = ""; // filtrēšanas lauki
            
            $col_nr = 0;
            
            if (!$this->view_row->is_report) {
                $htm_head .=    view('grid.heading_col', [
                                         'width' => '100',
                                         'fld_title' => trans('grid.lbl_actions'),
                                         'fld_name' => '',
                                         'sort_dir' => ''
                                    ])->render();
                $htm_flt .=     view('grid.filter_lbl_col')->render();
            }
                    
            for ($i=0; $i<count($view->model);$i++)
            {
                if ($this->isFieldIncludable($view->model[$i]))
                {               
                    $col_nr++;
                    
                    $htm_head .=    view('grid.heading_col', [
                                         'width' => $view->model[$i]["width"],
                                         'fld_title' => $view->model[$i]["label"],
                                         'fld_name' => $view->model[$i]["name"],
                                         'sort_dir' => $this->getSortingDirection($view->model[$i]["name"])
                                    ])->render();
                    
                    $htm_flt .=     view('grid.filter_col', [
                                         'fld_name' => $view->model[$i]["name"],
                                         'field_id' => $view->model[$i]["field_id"],
                                         'col_nr' => $col_nr,
                                         'filtr_val' => $this->getFilteringValue($view->model[$i]["name"])
                                    ])->render();
                                        
                    if (isset($view->summaryrows[$view->model[$i]["name"]]))
                    {
                            if (strlen($this->sql_sum_fld) > 0)
                            {
                                    $this->sql_sum_fld .= ", ";
                            }

                            $this->sql_sum_fld .= "SUM(" . $view->model[$i]["name"] . ") as " . $view->model[$i]["name"];
                    }
                }
            }
            
            return  view('grid.heading_row', [
                         'grid_id' => $this->grid_id,
                         'filters' => $htm_flt,
                         'headings' => $htm_head,
                         'is_filter' => ($this->filter_data && $this->filter_data != '[]')
                    ])->render();
        }
        
        /**
        * Funkcija nosaka, vai laukam ir jāattēlo kārtošanas ikona un kāda
        * 
        * @param string @fld_name Lauka nosaukums datu bāzē
        * @return string Atgriež tukšumu, "up" vai "down" - šīs vērtības tiek padotas Blade skatam, lai attēlotu ikonu
        */ 
        private function getSortingDirection($fld_name)
        {
            $sort_icon = "";
            if ($this->sorting_field == $fld_name)
            {					
                if ($this->sorting_direction == 1)
                {
                    $sort_icon = "up"; // asc
                }
                else
                {
                    $sort_icon = "down"; // desc
                }
            }
            return $sort_icon;
        }
        
        /**
        * Funkcija nosaka, vai laukam ir bijusi norādīta filtrēšana no saskarnes (analizē filtra JSON vērtību)
        * 
        * @param string @fld_name Lauka nosaukums datu bāzē
        * @return string Atgriež tukšumu, ja nav filtrs, vai arī filtrējamo vērtību
        */ 
        private function getFilteringValue($fld_name)
        {
            $view = $this->view->view_obj;
            $arr_filt = $this->filter_obj->arr_val;
            
            $flt_val = "";
            if (isset($arr_filt[":" . $fld_name]) > 0)
            {
                $flt_val = $arr_filt[":" . $fld_name];
                $flt_val = substr($flt_val, 1, strlen($flt_val)-2);
            }
            
            return $flt_val;
        }
        
        /**
        * Funkcija nosaka izmantojamās formas URL - tas tiks lietots AJAX pieprasījumos, lai atvērtu ieraksta apskates/rediģēšanas formu
        * 
        * @return string Formas URL vai arī tukšums
        */         
        private function setGridFormURL()
        {
            $first_form = DB::table('dx_forms')->where('list_id','=',$this->list_id)->first();
            
            if ($first_form)
            {
                if ($first_form->form_type_id == 2)
                {
                    $this->form_url = $first_form->custom_url;                        
                }
                else if ($first_form->form_type_id == 3) {
                    $this->profile_url = Config::get('dx.employee_profile_page_url', '');
                }
                else
                {
                    $this->form_url = "form";
                }
                
                $this->form_type_id = $first_form->form_type_id;
                $this->form_id = $first_form->id;
            }
        }
        
        /**
        * Funkcija sakonkatinē HTML atribūtus, kas nepieciešami, lai funkcionētu JavaScript funkcijas datu pārlādēšanai un filtrēšanai
        * 
        * @return string HTML atribūti
        */ 
        private function getHTMLDataAttributes()
        {
            $htm_prop = "";
            
            $htm_prop .= " data-grid_data_htm_id='" . $this->grid_data_htm_id . "'";
            $htm_prop .= " data-tab_id='" . Request::input('tab_id', '') . "'";
            $htm_prop .= " data-form_htm_id='" . $this->form_htm_id . "'";
            $htm_prop .= " data-list_id='" . $this->list_id . "'";
            $htm_prop .= " data-view_id='" . $this->view_id . "'";
            $htm_prop .= " data-grid_page_nr='" . $this->grid_page_nr . "'";
            $htm_prop .= " data-grid_rows_in_page='" . $this->grid_rows_in_page . "'";
            $htm_prop .= " data-sorting_field='" . $this->sorting_field . "'";
            $htm_prop .= " data-sorting_direction='" . $this->sorting_direction . "'";
            $htm_prop .= " data-rel_field_id='" . Request::input('rel_field_id', 0) . "'";
            $htm_prop .= " data-rel_field_value='" . Request::input('rel_field_value', 0) . "'";
            $htm_prop .= " data-page_nr='" . Request::input('page_nr', 1) . "'";
            $htm_prop .= " data-page_row_count='" . Request::input('page_row_count', 20) . "'";
            $htm_prop .= " data-filter_data='" . Request::input('filter_data', '') . "'";
            
            return $htm_prop;
        }
        
        /**
        * Funkcija formatē lauka vērtību kā saiti, no kuras ar JavaScript atveras forma. Saite nevar būt uz datnes lauka un sarakstam ir jābūt definētai formai
        * 
        * @param mixed $cell_obj Lauka objekts
        * @param Array $model_row Lauka atribūtu masīvs
        * @param Array $data_row Datu rindas masīvs (ar visu lauku vērtībām)
        * @return mixed Lauka objekts, kura nepieciešamības gadījumā ir formatēta vērtība kā saite
        */ 
        private function formatLinkValue($cell_obj, $model_row, $data_row)
        {
            if ($cell_obj->is_link) {
                return $cell_obj;
            }
            
            $method_name = "";
            if ($model_row['is_link'] && $model_row['type'] != 'file' && strlen($this->form_url) > 0)
            {
                $method_name = "getLinkCell";            }
            
            if ($this->form_type_id == 3 && strlen($this->profile_url) > 0) {
                $method_name = "getLinkProfileCell";
            }
            
            if ($method_name) {
                $cell_obj->value =  $this->helper->$method_name([                                         
                                         'item_id' => $data_row["id"],                                         
                                         'cell_value' => $cell_obj->value
                                    ]);
                $cell_obj->is_html = true;
            }
            
            return $cell_obj;
        }
        
        /**
         * Prepares HTML with totals in case there are some SUM rows
         * @return string
         */
        private function getSumRow() {
            if (strlen($this->sql_sum_fld) == 0 || $this->grid_total_rows == 0) {                
                return ""; // nothing to SUM
            }                    
            
            // add summary row
            $sql_sum = "SELECT " . $this->sql_sum_fld . " FROM (" . $this->sum_sql . ") tb";
            
            DB::setFetchMode(PDO::FETCH_ASSOC);
            $sum_rows = DB::select($sql_sum, $this->filter_obj->arr_filt);
            DB::setFetchMode(PDO::FETCH_CLASS);
            
            if (count($sum_rows) == 0) {
                return "";
            }
                            
            
            $row_s = $sum_rows[0];
            $view = $this->view->view_obj;
            
            $tr_htm = "<tr style='background-color: #F7FACF'>";
            $colspan = $this->view_row->is_report ? 0 : 1;
            $is_total = 0;
            
            for ($i=0; $i<count($view->model);$i++)
            {
                if (strlen($view->model[$i]["label"]) > 0 && strlen($view->model[$i]["name"]) > 0)
                {
                    if (isset($row_s[$view->model[$i]["name"]]))
                    {
                        if ($colspan > 0 && $is_total == 0)
                        {
                                $tr_htm = $tr_htm . "<td colspan=" . $colspan . " align=right><b>" . trans('grid.lbl_total') . ":</b></td>";	
                        }
                        $tr_htm = $tr_htm . "<td align=right><b>" . $row_s[$view->model[$i]["name"]] . "</b></td>";
                        $is_total = 1;
                    }
                    else
                    {
                        if ($is_total == 0)
                        {
                                $colspan++;
                        }
                        else
                        {
                                $tr_htm = $tr_htm . "<td>&nbsp;</td>";
                        }
                    }
                }
            }
            
            $tr_htm = $tr_htm . "</tr>";
            return $tr_htm;   
                            
                    
        }
    }
}
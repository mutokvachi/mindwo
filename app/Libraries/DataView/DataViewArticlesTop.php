<?php
namespace App\Libraries\DataView {
    
    use Webpatser\Uuid\Uuid;
    use DB;
    use Config;
    use Log;
    use Request;
    use App\Exceptions;
    
    class DataViewArticlesTop extends DataView {
        
        /**
        *
        * Aktuālo ziņu boloka klase
        *
        *
        * Objekts nodrošina datu attēlošanu TOP ziņu blokā slaidrādes veidā
        *
        */
        
        public $form_url = "";
        
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
        * Inicializē tabulāra saraksta klases objektu.
        * 
        * @param int $view_id Skata identifikatrs (no tabulas dx_views lauks id)
        * @param mixed $filter_data JSON formātā filtrēšanas lauku vērtības
        * @param string $session_guid Unikāla guid vērtība vai arī tukšums. SQL objekti tiek glabāti sesijā, lai uzlabotu ātrdarbību
        * @return void
        */        
        public function __construct($view_id, $filter_data, $session_guid)
        {
            $this->initObjects($view_id, $filter_data, $session_guid, 0);
            
            $this->form_url = $this->setGridFormURL();
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
            return  view('grid.table', [
                         'grid_id' => $this->grid_id,
                         'data_attr' => $this->getHTMLDataAttributes(),
                         'table_head' => $this->getHeadingsRow(),
                         'table_body' => $this->getDataRows()
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

            $cnt = "";
            if ($this->grid_total_pages == 1)
            {
                    $cnt = "Ierakstu skaits: " . $this->grid_total_rows;
            }
            else
            {
                    $cnt = "Ieraksti " . $start_row . " līdz " . $end_row . " no " . $this->grid_total_rows; 
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
                        'record_count' => $cnt,
                        'is_paginator' => $this->grid_is_paginator,
                        'prev_page' => $prev_page,
                        'grid_page_nr' => $this->grid_page_nr,
                        'grid_total_pages' => $this->grid_total_pages,
                        'next_page' => $next_page
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
            $sql_order = "";
            if (strlen($this->view->view_obj->sql_orderby) > 0)
            {
                $sql_order = " ORDER BY " . $this->view->view_obj->sql_orderby;
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
                
                $sql_count = str_replace('*', 'COUNT(*) as cnt', $sql_count);
                
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
            
            foreach($rows as $row)
            {   
                $cell_htm = "";
                for ($i=0; $i<count($view->model);$i++)
                {
                    if ($this->isFieldIncludable($view->model[$i]))
                    {
                        $cell_obj = Formatters\FormatFactory::build_field($view->model[$i], $row);
                        
                        $cell_obj = $this->formatLinkValue($cell_obj, $view->model[$i], $row);

                        $cell_htm .= view('grid.data_col', ['align' => $cell_obj->align, 'cell_value' => $cell_obj->value])->render();
                    }
                }

                $htm .= view('excel.data_row', ['htm' => $cell_htm])->render();
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
                                         'col_nr' => $col_nr,
                                         'filtr_val' => $this->getFilteringValue($view->model[$i]["name"])
                                    ])->render();
                }
            }
            
            return  view('grid.heading_row', [
                         'grid_id' => $this->grid_id,
                         'filters' => $htm_flt,
                         'headings' => $htm_head
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
            $arr_filt = $this->filter_obj->arr_filt;
            
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
                    return $first_form->custom_url;                        
                }
                else
                {
                    return "form";
                }
            }
            
            return ""; // sarakstam nav definēta forma
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
            if ($model_row['is_link'] && $model_row['type'] != 'file' && strlen($this->form_url) > 0)
            {
                $cell_obj->value =  view('grid.cell_link', [
                                         'grid_form' => $this->form_url,
                                         'item_id' => $data_row["id"],
                                         'list_id' => $model_row["list_id"],
                                         'rel_field_id' => $this->view->view_obj->rel_field_id,
                                         'rel_field_value' => $this->view->view_obj->rel_field_value,
                                         'grid_id' => $this->grid_id,
                                         'form_htm_id' => $this->form_htm_id,
                                         'cell_value' => $cell_obj->value
                                    ])->render();
            }
            
            return $cell_obj;
        }
    }
}
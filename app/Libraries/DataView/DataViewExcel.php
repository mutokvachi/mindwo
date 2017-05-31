<?php

namespace App\Libraries\DataView
{

    use Config;

    /**
     *
     * Excel eksporta klase
     *
     *
     * Objekts nodrošina saraksta datu eksportu uz Excel
     *
     */
    class DataViewExcel extends DataView
    {

        /**
         * Indicates if there is at least 1 data row
         * @var boolean 
         */
        public $is_rows = false;
        
        /**
         * Array with Excel column letters and formaters for date and date/time columns
         * @var array 
         */
        public $formaters = [];
        
        /**
         * Inicializē Excel eksporta klases objektu.
         * 
         * @param int $view_id Skata identifikatrs (no tabulas dx_views lauks id)
         * @param mixed $filter_data Pašreizējā Excel eksporta realizācijā netiek izmantots, gridiem tas ir JSON formātā filtrēšanas lauku vērtības
         * @param string $session_guid Unikāla guid vērtība vai arī tukšums. SQL objekti tiek glabāti sesijā, lai uzlabotu ātrdarbību
         * @return void
         */
        public function __construct($view_id, $filter_data, $session_guid)
        {
            $this->initObjects($view_id, $filter_data, $session_guid, 0);
        }

        /**
         * Atgriež saraksta datus HTML formātā.
         * Atgriež kolonnu virsrakstus <thead> un datu rindas <tbody>
         * 
         * @return string saraksta datu HTML
         */
        public function getViewHtml()
        {
            $data = $this->getDataRows();
            
            $this->is_rows = (strlen($data) > 0);
            return $this->getHeadingsRow() . $data;
        }

        /**
         * Funkcijā tiek definēta SQL daļa Excel datu kārtošanai
         * Excel dati tiek kārtoti tā, kā definēts skatā, t.i., saskarnē norādītā kārtošana pēc kolonnas eksportā netiek ņemta vērā
         * Šī funkcija ir "protected" - tā tiek izpildīta vecāka objektā DataView, kad tiek konkatinēts izpildāmais datu atslases SQL
         *
         * @return string SQL kārtošanas fragments (ORDER BY)
         */
        protected function getSortingSQL()
        {
            $sql_order = "";
            if (strlen($this->view->view_obj->sql_orderby) > 0) {
                $sql_order = " ORDER BY " . $this->view->view_obj->sql_orderby;
            }

            return $sql_order;
        }

        /**
         * Funkcijā tiek definēta SQL daļa Excel datu porcijas lielumam (ierakstu skaits)
         * Maksimālais uz Excel eksportējamo ierakstu skaits ir 10000. Eksportēti tiek ieraksti sākot no pirmā (tātad, neņemot vērā saskarnē veikto ierakstu lapošanu)
         * Šī funkcija ir "protected" - tā tiek izpildīta vecāka objektā DataView, kad tiek konkatinēts izpildāmais datu atslases SQL
         * 
         * @return string SQL ierakstu porcijas fragments (LIMIT)
         */
        protected function getLimitSQL()
        {
            $limit_start = 0;
            $limit_end = Config::get('dx.excel_export_maximum_rows');

            return " LIMIT " . $limit_start . ", " . $limit_end;
        }

        /**
         * Funkcija izveido HTML ar Excel kolonnu virsrakstiem - tags <thead>
         * 
         * @return string HTML ar kolonnu virsrakstiem (tags <thead>)
         */
        private function getHeadingsRow()
        {
            $view = $this->view->view_obj;
            $htm = "";

            for ($i = 0; $i < count($view->model); $i++) {
                if ($this->isFieldIncludable($view->model[$i])) {
                    $htm .= view('excel.heading_col', ['title' => $view->model[$i]["label"]])->render();
                }
            }

            return view('excel.heading_row', ['htm' => $htm])->render();
        }

        /**
         * Funkcija izveido HTML ar Excel datu rindām - tags <tbody>
         * 
         * @return string HTML ar datu rindām (tags <tbody>)
         */
        private function getDataRows()
        {
            $rows = $this->getViewDataArray();
            $view = $this->view->view_obj;

            $htm = "";
                        
            foreach ($rows as $row) {
                $cell_htm = "";
                $col = 0;
                for ($i = 0; $i < count($view->model); $i++) {
                    if ($this->isFieldIncludable($view->model[$i])) {
                        $col ++;
                        $cell_obj = Formatters\FormatFactory::build_field($this->resetFieldType($view->model[$i], $row), $row, true);

                        $cell_htm .= view('excel.data_col', [
                            'align' => $cell_obj->align, 
                            'cell_value' => $cell_obj->value,
                            'is_val_html' => $cell_obj->is_html
                        ])->render();
                        
                        $this->setDateTimeCols($view->model[$i], $col);
                    }
                }

                $htm .= view('excel.data_row', ['htm' => $cell_htm])->render();
            }

            return $htm;
        }
        
        /**
         * Sets Excel columns formaters for date and datetime columns - stores formats in array
         * @param array $model_row Field properties
         * @param integer $col Column number
         */
        private function setDateTimeCols($model_row, $col) {
            if ($model_row['type'] == 'date') {
                $this->formaters[$this->getNameFromNumber($col)] = Config::get('dx.date_format');
            }
            
            if ($model_row['type'] == 'datetime') {
                $this->formaters[$this->getNameFromNumber($col)] = Config::get('dx.date_format') . ' hh:mm';
            }
        }
        
        /**
         * Gets Excel column letter according to column number
         * @param integer $num Excel column number starting from 1
         * @return string Excel column letter (or several letters if more columns)
         */
        private function getNameFromNumber($num) {
            $numeric = ($num - 1) % 26;
            $letter = chr(65 + $numeric);
            $num2 = intval(($num - 1) / 26);
            if ($num2 > 0) {
                return $this->getNameFromNumber($num2) . $letter;
            } else {
                return $letter;
            }
        }

        /**
         * Funkcija pārveido lauka tipu uz tekstu, ja tas ir file vai arī kā saite - jo Excel nav nepieciešams attēlot saites vai lejupielādēt datnes
         * 
         * @param Array $model_row Masīvs ar lauka atribūtiem
         * @return Array Masīvs ar lauka atribūtiem (nepieciešamības gadījumā pamainīts lauka tips)
         */
        private function resetFieldType($model_row)
        {
            if ($model_row['type'] == 'file') {
                $model_row['type'] = 'varchar';
            }
            
            $model_row['is_link'] = 0;

            return $model_row;
        }

    }

}
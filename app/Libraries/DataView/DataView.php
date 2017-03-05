<?php
namespace App\Libraries\DataView {
    
    use App\Exceptions;
    
    use DB;
    use PDO;
    
    abstract class DataView
    {
        
        public $view_id = 0;
        public $list_id = 0;
        public $view = null;
        public $filter_obj = null;
        public $is_PDO = true;
        
        abstract function getViewHtml();
        
        abstract protected function getSortingSQL();
        abstract protected function getLimitSQL();
                
        protected function initObjects($view_id, $filter_data, $session_guid, $is_hidden_in_model)
        {                
            $this->view_id = $view_id;
            
            try
            {
                $this->list_id = DB::table('dx_views')->where('id','=',$view_id)->first()->list_id;
            } 
            catch (\Exception $ex) 
            {
                throw new Exceptions\DXCustomException("Norādīts neeksistējošs datu skatījuma identifikators (" . $view_id . ")!");
            }
            
            $this->validateIDField();
            
            $this->view = DataViewFactory::build_view_obj($this->list_id, $this->view_id, $session_guid, $is_hidden_in_model);
            
            $this->filter_obj = new DataViewSQLFiltering($filter_data, $this->list_id, $this->view);
        }
        
        public function getViewTitle()
        {
            return DB::table('dx_views')->where('id','=',$this->view_id)->first()->title;
        }
        
        protected function getViewDataArray()
        {
            $sql = $this->view->view_sql . " " . $this->filter_obj->sql . " " . $this->getSortingSQL() . " " . $this->getLimitSQL();

            if ($this->is_PDO) {
                DB::setFetchMode(PDO::FETCH_ASSOC); // We need to get vields values dynamicly where field names are from array                    
            }
            
            $rows = DB::select($sql, $this->filter_obj->arr_filt);                 
            
            if ($this->is_PDO) {
                DB::setFetchMode(PDO::FETCH_CLASS); // Set back default fetch mode
            }
            return $rows;
        }
        
        /**
        * Funkcija pārbauda, vai skata lauku var iekļaut gridā/excel
        * Gridā/excel var iekļaut lauksu, kuriem ir virsraksts un kuriem ir datu bāzes lauka nosaukums
        * 
        * @param Array $model_row Masīvs ar skata datu modeļa ierakstu. No masīva tiek izmantotas vērtības label un name.
        * @return bool Atgriež true, ja lauku var iekļaut gridā/excel vai false, ja nevar
        */         
        protected function isFieldIncludable($model_row)
        {
            if (strlen($model_row["label"]) > 0 && strlen($model_row["name"]) > 0)
            {
                return true;
            }
            
            return false;
        }
        
        /**
         * Pārbauda, vai skatā un reģistrā ir iekļauts ID lauks. Ja nav tad izveido/iekļauj
         * Visos skatos ir obligāti jāiekļauj ID lauks - uz tā tiek balstīta saistīto formu funkcionalitāte kā arī noklusētās kārtošanas loģika 
         * 
         * @return void
         */
        private function validateIDField()
        {
            $id_field_row = DB::table('dx_lists_fields')->where('list_id','=',$this->list_id)->where('db_name','=','id')->first();

            if (!$id_field_row)
            {
                // reģistram nav nodefinēta ID kolonna, izveidojam to
                $id_field = $this->addIDFieldToList();
                $this->insertIDField($id_field); // pievienojam ID lauku skatam
            }
            else
            {
                // pārbaudam vai lauks ir iekļauts skatā
                $view_id_field_row = DB::table('dx_views_fields')->where('field_id','=',$id_field_row->id)->first();
                if (!$view_id_field_row)                    
                {
                    $this->addIDFieldToView($id_field_row->id); // pievienojam ID lauku skatam
                }
            }
        }

        /**
         * Pievieno ID lauku skata laukiem
         * Visos skatos ir obligāti jāiekļauj ID lauks - uz tā tiek balstīta saistīto formu funkcionalitāte kā arī noklusētās kārtošanas loģika 
         * 
         * @return void
         */
        private function addIDFieldToView($id_field)
        {
            DB::table('dx_views_fields')->insert(['list_id'=>$this->list_id, 'view_id' => $this->view_id, 'field_id' => $id_field, 'is_hidden' => 1]);
        }

        /**
         * Pievieno ID lauku reģistra laukiem
         * Visos reģistros ir obligāti jāiekļauj ID lauks - uz tā tiek balstīta saistīto formu funkcionalitāte kā arī noklusētās kārtošanas loģika
         * 
         * @return void
         */
        private function addIDFieldToList()
        {            
            $this->validateIDFieldExistance();
            return DB::table('dx_lists_fields')->insertGetId(['list_id' => $this->list_id, 'db_name' => 'id', 'type_id' => 6, 'title_list' => 'ID', 'title_form' => 'ID']);
        }

        /**
         * Pārbauda vai reģistra tabulai datu bāzē ir id lauks
         * Visām tabulām datu bāzē ir obligāti jābūt id laukam
         * 
         * @return void
         */
        private function validateIDFieldExistance()
        {
            $obj_id = DB::table('dx_lists')->where('id', '=', $this->list_id)->first()->object_id;
            $table_name = DB::table('dx_objects')->where('id', '=', $obj_id)->first()->db_name;
            
            if (!Schema::hasColumn($table_name, 'id'))
            {
                throw new Exceptions\DXCustomException("Datu bāzes tabulai '" . $table_name . "' nav definēts id lauks!");
            }
        }
    }
}

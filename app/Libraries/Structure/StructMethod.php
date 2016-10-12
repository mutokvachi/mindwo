<?php
namespace App\Libraries\Structure {
    
    use Webpatser\Uuid\Uuid;
    use DB;
    
    abstract class StructMethod
    {        
        /**
        *
        * Sistēmas struktūras veidošanas metodes abstraktā klase
        *
        *
        * Definē visām struktūras metodēm kopīgās funkcijas.
        *
        */
        
        public $form_guid = "";
        abstract function getFormHTML();        
        
        abstract function doMethod();    
        
        abstract protected function initData();
        
        public function __construct()
        {
            $this->initData();
            $this->form_guid = Uuid::generate(4);
        }
        
        /**
         * Izgūst tabulas SVS konfigurēta objekta datus pēc norādītā tabulas nosaukuma.
         * Objekts tiek izmantots vēstures veidošanas funkcijai.
         * 
         * @param string $table_name Tabulas nosaukums datu bāzē
         * @throws Exceptions\DXCustomException
         */
        public function getObjTable($table_name)
        {
            $tbl = DB::table('dx_objects as o')
                        ->leftJoin('dx_lists as l','o.id', '=', 'l.object_id')
                        ->select(DB::raw('o.db_name as table_name, o.is_history_logic, l.id as list_id'))
                        ->where('o.db_name', '=', $table_name)
                        ->first();

            if (!$tbl) {
                throw new Exceptions\DXCustomException("Tabulai nav definēts datu objekts un/vai reģistrs!");
            }

            return $tbl;
        }
    }
}

<?php
namespace App\Libraries\FieldsSave {

    use \App\Exceptions;
    use App\Libraries\Rights;
    use DB;
    
    abstract class FieldSave
    {        
        /**
        *
        * Formas lauka saglabāšanas abstraktā klase
        * Definē visiem formu laukiem kopīgās saglabāšanas metodes.
        *
        */
        
        /**
         * Pazīme, vai tiek saglabāti vienlaicīgi vairāki ieraksti (piemēram, augšuplādētas vairākas datnes) 
         * @var boolean
         */
        public $is_multi_val = 0;
        
        /**
         * Pazīme, vai ir uzstādīta lauka vērtība
         * @var boolean
         */
        public $is_val_set = 0;
        
        /**
         * POST/GET pieprasījuma objekts
         * @var Request
         */
        public $request = null;
        
        /**
         * Lauka objekts, kas satur lauka parametrus no tabulas dx_lists_fields
         * @var Object
         */
        public $fld = null;
        
        /**
         * Masīvs ar lauka vērtībām
         * @var Array
         */
        public $val_arr = array();
        
        /**
         * Masīvs ar lauka tekstuālajām vērtībām. 
         * Tiek izmantots, lai atjauninātu HTML formas datus pēc saglabāšanas (piemēram, ar datumiem, reģ. nr. utt)
         * @var Array
         */
        public $txt_arr = array();
        
        /**
         * Ieraksta identifikators. Ja nav norādīts, tad jauns ieraksts
         * @var integer 
         */
        public $item_id = 0;
        
        /**
         * Masīvs ar saistīto ierakstu sinhronizēšanas norādījumiem (tabula, lauks)
         * Tas nepieciešams lai, piemēram, atbildes un nosūtāmo dokumentu gadījumā, viens otram uzstādītu vērtības
         * Bet šajā klasē, vēl ieraksts var būt arī bez ID - tāpēc klasei definēts šis parametrs un sasinhronizēšana tiks veikta vēlāk
         * 
         * @var Array 
         */
        public $upd_rel_arr = array();
        
        /**
         * Apstrādā lauka vērtības
         */
        abstract protected function prepareVal();
        
        /**
        * Atgriež formas lauka vērtību masīvu
        * Ja $is_multi_val ir 1, tad atgriež vairākas vērtības, kas masīvā indeksētas 0, 1, 2...
        *
        * @return Array Masīvs ar lauka vērtību (vai vairākām)
        */
        public function getVal()
        {
            return $this->val_arr;
        }
        
        /**
         * Atgriež lauka tekstuālo vērtību masīvu, kas tiek izmantots, lai atjauninātu HTML formas datus pēc saglabāšanas (piemēram, ar datumiem, reģ. nr. utt)
         * @return Array Masīvs ar lauka tekstuālajām vērtībām
         */
        public function getTxtArr()
        {
            return $this->txt_arr;
        }
       
        /**
        * Formas lauka konstuktors
        *
        * @param  Request $request POST/GET pieprasījuma objekts 
        * @return void
        */
        public function __construct($request, $fld, $item_id)
        {            
            $this->request = $request;
            $this->fld = $fld;
            $this->item_id = $item_id;
            
            $this->prepareVal();
            
            $this->checkRequired();
        }
        
        /**
         * Pārbauda, vai saistīto ierakstu lietotājam ir pieļaujams izvēlēties
         * 
         * @param integer $id Saistītā ieraksta ID
         * @throws Exceptions\DXCustomException
         */
        public function validateDataSource($id) {
            
            $sql = "SELECT * FROM " . $this->fld->rel_table_name . " WHERE id = " . $id . Rights::getSQLSourceRights($this->fld->rel_list_id, $this->fld->rel_table_name);
            
            $row = DB::select($sql);
            
            if (count($row) == 0) {
                throw new Exceptions\DXCustomException("Nevar saglabāt datus! Nav tiesību norādīt saistīto ierakstu ar ID " . $id . "!"); 
            }            
        }
        
        /**
         * Pārbauda lauka vērtības obligātumu
         * @throws Exceptions\DXCustomException
         */
        private function checkRequired()
        {
            if ($this->fld->is_required == 1 && !$this->is_val_set && $this->request->has($this->fld->db_name))
            {                
                throw new Exceptions\DXCustomException(sprintf(trans('errors.required_field'), $this->fld->title_form));                
            }
        }
    }
}

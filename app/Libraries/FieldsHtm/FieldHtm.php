<?php

namespace App\Libraries\FieldsHtm
{

    /**
     * Formas lauka attēlošanas abstraktā klase.
     * Definē visiem formu laukiem kopīgās attēlošanas metodes.
     */
    abstract class FieldHtm
    {

        /**
         * Lauka vērtība. Uzstāda noklusēto, ja veido jaunu ierakstu (var būt ID, datums, teksts u.c.)
         * 
         * @var mixed
         */
        public $item_value = null;
                
        /**
         * Masīvs ar lauka atribūtiem
         * 
         * @var Array 
         */
        public $fld_attr = null;

        /**
         * Ieraksta ID. Ja rediģēšana, tad tas ir lielāks par 0
         * 
         * @var integer 
         */
        public $item_id = 0;
        
        /**
         * Ieraksta reģistra ID
         * @var integer
         */
        public $list_id = 0;
        
        /**
         * Formas unikālais HTML identifikators.
         * Nepieciešams, lai lauks būtu viennozīmīgi unikāls attēlotajā web lapā.
         * 
         * @var string
         */
        public $frm_uniq_id = "";
        
        /**
         * Pazīme, vai forma ir nerediģējamā režīmā (tad lauks arī tiks attēlots nerediģējams)
         * 
         * @var integer
         */
        public $is_disabled_mode = 0;
        
        /**
         * Atgriež lauka attēlošanas HTML
         */
        abstract function getHtm();
        
        /**
         * Uzstāda noklusēto lauka vērtību jauna ieraksta gadījumā
         */
        abstract protected function setDefaultVal();

        /**
         * Formas lauka konstuktors
         *
         * @param  Array $fld_attr Masīvs ar lauka parametriem 
         * @return void
         */
        public function __construct($fld_attr, $item_id, $item_value, $list_id, $frm_uniq_id, $is_disabled_mode)
        {
            $this->fld_attr = $fld_attr;
            $this->item_id = $item_id;
            $this->list_id = $list_id;
            $this->frm_uniq_id = $frm_uniq_id;
            $this->is_disabled_mode = $is_disabled_mode;
            $this->item_value = $item_value;
            
            $this->setDefaultVal();
        }

    }

}

<?php

namespace App\Libraries\Structure\Types
{    
    abstract class FieldType
    {
        /**
          *
          * Sistēmas struktūras reģistra lauka tipa abstraktā klase
          *
          *
          * Definē visiem lauku tipiem kopīgās funkcijas.
          *
         */

        public $field_id = 0;
        public $field_obj = null;
        public $list_id = 0;
        public $field_name = "";
        public $field_title = "";
        public $is_required = 0;
        public $table_name = "";
        
        public $is_text_extract = 0;
        /**
         * Field type class constructor
         * 
         * @param string $table_name Table name
         * @param integer $list_id Register ID
         * @param string $field_name Field name in db
         * @param object $field_obj Column info returned by DB::connection()->getDoctrineColumn
         * @param boolean $is_text_extract Is text exstraction from file field
         */
        public function __construct($table_name, $list_id, $field_name, $field_obj, $is_text_extract = 0)
        {
            $this->field_obj = $field_obj;
            $this->list_id = $list_id;
            $this->field_name = $field_name;
            $this->field_title = $this->getFieldTitle();
            $this->is_required = $this->getFieldRequired();
            $this->table_name = $table_name;
            $this->is_text_extract = $is_text_extract;
            
            $this->initField();
            
            /*
            try {
                Log::info("Methods: " . json_encode(get_class_methods($this->field_obj)));
            }
            catch (\Exception $e) {
                Log::info($e->getMessage());
            }            
            */
        }

        /**
         * Atgriež izveidotā lauka ID
         * 
         * @return integer Lauka ID
         */

        public function getFieldID()
        {
            return $this->field_id;
        }

        abstract protected function initField();

        /**
         * Atgriež lauka tekstuālo nosaukumu, kas tiks lietots formā un sarakstā
         * 
         * @return string Lauka tekstuālais nosaukums
         */

        private function getFieldTitle()
        {
            if ($this->field_name == "id")
            {
                return "ID";
            }
            
            $title = $this->field_obj->getComment();
            if (strlen($title) == 0)
            {
                $title = $this->field_name;
            }

            return $title;
        }

        /**
         * Atgriež lauka obligātumu
         * 
         * @return integer Lauka obligātums
         */

        private function getFieldRequired()
        {
            if ($this->field_name == "id")
            {
                return 1;
            }
            
            $is_required = $this->field_obj->getNotnull();
            if (!$is_required)
            {
                $is_required = 0;
            }

            return $is_required;
        }

    }

}

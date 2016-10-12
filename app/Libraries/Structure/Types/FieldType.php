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
        
        /**
         * Lauka tipa konstruktors
         * 
         * @param integer $list_id      Reģistra ID
         * @param string  $field_obj    Tabulas lauka objekts
         * @return void
         */

        public function __construct($table_name, $list_id, $field_name, $field_obj)
        {
            $this->field_obj = $field_obj;
            $this->list_id = $list_id;
            $this->field_name = $field_name;
            $this->field_title = $this->getFieldTitle();
            $this->is_required = $this->getFieldRequired();
            $this->table_name = $table_name;
            
            $this->initField();
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

<?php

namespace App\Libraries\Structure\Types
{
    use DB;
    
    class FieldType_id extends FieldType
    {
        /**
          *
          * ID lauka klase
          *
          *
          * Objekts nodrošina ID lauka izveidošanu
          *
         */
                
         /**
         * Inicializē lauku
         * 
         * @return void
         */
        public function initField()
        {
            $this->field_id = DB::table('dx_lists_fields')->insertGetId(
                array('list_id' => $this->list_id, 'db_name' => $this->field_name, 'type_id' => 6, 'title_list' => 'ID', 'title_form' => 'ID')
            );
        }

    }

}
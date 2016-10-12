<?php

namespace App\Libraries\Structure\Types
{
    use DB;
    
    class FieldType_datetime extends FieldType
    {
        /**
          *
          * Datuma/laika lauka klase
          *
          *
          * Objekts nodrošina datuma/laika lauka izveidošanu
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
                array(
                    'list_id' => $this->list_id, 
                    'db_name' => $this->field_name, 
                    'type_id' => 2, // Datums/Laiks
                    'title_list' => $this->field_title, 
                    'title_form' => $this->field_title,
                    'is_required' => $this->is_required
                    )
            );
        }

    }

}
<?php

namespace App\Libraries\Structure\Types
{
    use DB;
    
    class FieldType_boolean extends FieldType
    {
        /**
          *
          * Jā/Nē lauka klase
          *
          *
          * Objekts nodrošina Jā/Nē lauka izveidošanu
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
                    'type_id' => 7, // Jā/Nē
                    'title_list' => $this->field_title, 
                    'title_form' => $this->field_title,
                    'is_required' => $this->is_required
                    )
            );
        }

    }

}
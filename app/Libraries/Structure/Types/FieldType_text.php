<?php

namespace App\Libraries\Structure\Types
{
    use DB;
    
    class FieldType_text extends FieldType
    {
        /**
          *
          * Neierobežota teksta lauka klase
          *
          *
          * Objekts nodrošina neierobežota teksta lauka izveidošanu
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
                    'type_id' => 10, // HTML teksts
                    'title_list' => $this->field_title, 
                    'title_form' => $this->field_title,
                    'is_required' => $this->is_required
                    )
            );
        }

    }

}
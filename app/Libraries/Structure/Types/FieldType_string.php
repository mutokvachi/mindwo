<?php

namespace App\Libraries\Structure\Types
{
    use DB;
    
    class FieldType_string extends FieldType
    {
        /**
          *
          * Teksta lauka klase
          *
          *
          * Objekts nodrošina teksta lauka izveidošanu
          *
         */
                
         /**
         * Inicializē lauku
         * 
         * @return void
         */
        public function initField()
        {
            $lenght = $this->field_obj->getLength();
            
            $type_id = 1; // Teksts
            if ($lenght > 250)
            {
                $type_id = 4; // Garš teksts
            }
            
            $this->field_id = DB::table('dx_lists_fields')->insertGetId(
                array(
                    'list_id' => $this->list_id, 
                    'db_name' => $this->field_name, 
                    'type_id' => $type_id, 
                    'title_list' => $this->field_title, 
                    'title_form' => $this->field_title,
                    'max_lenght' => $lenght,
                    'is_required' => $this->is_required
                    )
            );
        }

    }

}
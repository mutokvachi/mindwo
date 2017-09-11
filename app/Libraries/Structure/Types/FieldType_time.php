<?php

namespace App\Libraries\Structure\Types
{
    use DB;
    
    /**
     * Time field class
     */
    class FieldType_time extends FieldType
    {
                        
        /**
         * Inits class
         */
        public function initField()
        {
            $this->field_id = DB::table('dx_lists_fields')->insertGetId(
                array(
                    'list_id' => $this->list_id, 
                    'db_name' => $this->field_name, 
                    'type_id' => 1, 
                    'title_list' => $this->field_title, 
                    'title_form' => $this->field_title,
                    'max_lenght' => 5,
                    'is_required' => $this->is_required
                    )
            );
        }

    }

}
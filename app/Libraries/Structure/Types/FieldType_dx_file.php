<?php

namespace App\Libraries\Structure\Types
{
    use DB;
    
    /**
     * CMS field class for file field
     */
    class FieldType_dx_file extends FieldType
    {
        /**
         * Initialises field - inserts into dx_lists_fields
         */
        public function initField(){
            $this->field_id =   DB::table('dx_lists_fields')->insertGetId(
                                    array(
                                        'list_id' => $this->list_id, 
                                        'db_name' => $this->field_name, 
                                        'type_id' => 12, 
                                        'title_list' => $this->field_title, 
                                        'title_form' => $this->field_title,
                                        'is_required' => $this->is_required,
                                        'is_text_extract' => $this->is_text_extract
                                        )
                                );
        }

    }

}
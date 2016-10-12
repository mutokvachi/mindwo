<?php

namespace App\Libraries\Structure\Types
{

    use DB;

    /**
     *
     * Decimāldaļas skaitļa lauka klase
     *
     *
     * Objekts nodrošina decimāldaļas skaitļa lauka izveidošanu
     *
     */
    class FieldType_decimal extends FieldType
    {

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
                        'type_id' => 18, // no tabulas dx_field_types
                        'title_list' => $this->field_title,
                        'title_form' => $this->field_title,
                        'is_required' => $this->is_required
                    )
            );
        }

    }

}
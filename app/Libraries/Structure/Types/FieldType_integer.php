<?php

namespace App\Libraries\Structure\Types
{

    use DB;
    use App\Exceptions;
    use Config;

    /**
     *
     * Skaitļa lauka klase
     *
     *
     * Objekts nodrošina skaitļa lauka izveidošanu
     *
     */
    class FieldType_integer extends FieldType
    {

        /**
         * Inicializē lauku
         * 
         * @return void
         */
        public function initField()
        {
            $type_id = 5; // Skaitlis (pēc noklusēšanas)

            $arr_flds = array(
                'list_id' => $this->list_id,
                'db_name' => $this->field_name,
                'title_list' => $this->field_title,
                'title_form' => $this->field_title,
                'is_required' => $this->is_required
            );

            $key = $this->getForeignKey();

            if ($key) {
                $rel_obj_row = DB::table('dx_objects')->where('db_name', '=', $key->ref_table)->first();

                if (!$rel_obj_row) {
                    throw new Exceptions\DXCustomException("Nav definēts datu objekts saistītā ieraksta tabulai '" . $key->ref_table . "'! Vispirms jāizveido saistītais objekts un reģistrs.");
                }

                $rel_list_row = DB::table('dx_lists')->where('object_id', '=', $rel_obj_row->id)->first();

                if (!$rel_list_row) {
                    throw new Exceptions\DXCustomException("Nav definēts reģistrs saistītā ieraksta tabulai '" . $key->ref_table . "'! Vispirms jāizveido saistītā objekta reģistrs.");
                }

                $rel_title_row = DB::table('dx_lists_fields as f')
                        ->select(DB::raw('f.id'))
                        ->leftJoin('dx_field_types as t', 't.id', '=', 'f.type_id')
                        ->where('f.list_id', '=', $rel_list_row->id)
                        ->whereIn('f.type_id', [1, 2, 9, 4])
                        ->where('f.db_name', '!=', 'created_user_id')
                        ->where('f.db_name', '!=', 'created_time')
                        ->where('f.db_name', '!=', 'modified_user_id')
                        ->where('f.db_name', '!=', 'modified_time')
                        ->orderBy('t.sys_name', 'DESC')
                        ->orderBy('f.id')
                        ->first();

                if (!$rel_title_row) {
                    throw new Exceptions\DXCustomException("Nav definēts neviens teksta lauks saistītā ieraksta tabulas '" . $key->ref_table . "' reģistram!");
                }

                $arr_flds['rel_list_id'] = $rel_list_row->id;
                $arr_flds['rel_display_field_id'] = $rel_title_row->id;

                $type_id = 3; // Saistītais ieraksts
            }
            else {
                if (FieldTypeFactory::startsWith($this->field_name, "is_")) {
                    $type_id = 7; // Jā/Nē
                }
            }

            $arr_flds['type_id'] = $type_id;

            $this->field_id = DB::table('dx_lists_fields')->insertGetId($arr_flds);
        }

        /**
         * Atgriež saistītās tabulas un lauka informāciju, ja laukam ir uzstādīts indekss un relācija
         * 
         * @return Array Masīvs ar saistītās tabula un lauka nosaukumu vai null, ja nav relācijas
         */
        private function getForeignKey()
        {
            $database_name = Config::get('database.connections.' . Config::get('database.default') . '.database');

            $sql = "select
                        REFERENCED_TABLE_NAME as ref_table,
                        REFERENCED_COLUMN_NAME as ref_col
                    from 
                        INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                    where
                        TABLE_NAME = '" . $this->table_name . "' and 
                        TABLE_SCHEMA = '" . $database_name . "' and 
                        COLUMN_NAME = '" . $this->field_name . "' and
                        REFERENCED_TABLE_NAME is not null
            ";

            $keys = DB::select($sql);
            if (count($keys) > 0) {
                return $keys[0];
            }

            return null;
        }

    }

}
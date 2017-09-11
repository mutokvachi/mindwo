<?php

namespace App\Libraries\Structure
{

    use Input;
    use DB;
    use App\Exceptions;
    use Log;
    use App\Libraries\Structure\Types;
    
    class StructMethod_register_generate extends StructMethod
    {
        /**
          *
          * Reģistra ģenerēšanas klase
          *
          *
          * Objekts nodrošina reģistra ģenerēšanu: reģistra lauki, forma, skats
          *
         */
        
        public $obj_id = 0;
        public $register_title = "";
        public $form_title = "";
        
        public $exclude_fields = ['created_user_id', 'created_time', 'modified_user_id', 'modified_time'];
        
         /**
         * Inicializē klases parametrus
         * 
         * @return void
         */
        public function initData()
        {
            $this->obj_id = Input::get('obj_id', 0);
            
            if ($this->obj_id == 0)
            {
                $this->obj_id = Input::get('item_id', 0);
            }
            
            $this->register_title = Input::get('register_title', '');
            $this->form_title = Input::get('form_title', '');
        }
        
        /**
         * Atgriež reģistra ģenerēšanas uzstādījumu HTML formu
         * 
         * @return string HTML forma
         */

        public function getFormHTML()
        {
            return view('structure.register_generate', [
                        'form_guid' => $this->form_guid,
                        'obj_id' => $this->obj_id
                    ])->render();
        }

        /**
         * Izveido reģistru: laukus, skatu, formu
         * 
         * @return void
         */

        public function doMethod()
        {
            $this->validateData();
            
            DB::transaction(function () {

                $obj_row = DB::table('dx_objects')->where('id', '=', $this->obj_id)->first();
                
                $list_id = DB::table('dx_lists')->insertGetId(
                    array('list_title' => $this->register_title, 'object_id' => $this->obj_id)
                );
                
                $view_id = DB::table('dx_views')->insertGetId(
                    array('list_id' => $list_id, 'title' => $this->register_title, 'view_type_id' => 1, 'is_default' => 1)
                );
                
                $form_id = DB::table('dx_forms')->insertGetId(
                    array('list_id' => $list_id, 'title' => $this->form_title, 'form_type_id' => 1)
                );
                
                $counter = 1;
                $fields = DB::getSchemaBuilder()->getColumnListing($obj_row->db_name);
                foreach($fields as $field)
                {
                    if (array_search($field, $this->exclude_fields)!==FALSE)
                    {
                        continue;
                    }
                    
                    $field_obj = $this->getFileFieldObj($obj_row->db_name, $list_id, $field, $fields);
                                        
                    if (!$field_obj) {
                        $field_obj = Types\FieldTypeFactory::build_type($obj_row->db_name, $list_id, $field);
                    }
                    
                    DB::table('dx_views_fields')->insert([
                        'list_id' => $list_id,
                        'view_id' => $view_id,
                        'field_id' => $field_obj->getFieldID(),
                        'width' => 0,
                        'order_index' => $counter*10,
                        'is_item_link' => 1
                    ]);

                    DB::table('dx_forms_fields')->insert([
                        'list_id' => $list_id,
                        'form_id' => $form_id,
                        'field_id' => $field_obj->getFieldID(),
                        'order_index' => $counter*10,
                        'is_readonly' => (($field == "id") ? 1:0)
                    ]);

                    $counter++;                    
                }
            });
        }
        
        /**
         * Check if field is file field (table have _name, _guid and maybe _dx_text field)
         * 
         * @param string $table_name Table name
         * @param integer $list_id Register ID
         * @param string $field Field name in db
         * @param array $fields Table all fields array
         * @return null|\App\Libraries\Structure\Types\FieldType_dx_file
         */
        private function getFileFieldObj($table_name, $list_id, $field, $fields) {
            if (!$this->endsWith($field, "_name")) {
                return null;
            }
            
            $file_field = str_replace("_name", "", $field);
                        
            if (array_search($file_field . "_guid", $fields) === FALSE) {
                return null;
            }
                        
            // is file field
            array_push($this->exclude_fields, $file_field . "_guid");

            $is_text_extract = 0;
            if (array_search($file_field . "_dx_text", $fields) !== FALSE) {
                // is text extract
                $is_text_extract = 1;
                array_push($this->exclude_fields, $file_field . "_dx_text");
            }

            $obj = DB::connection()->getDoctrineColumn($table_name, $field);
            return new Types\FieldType_dx_file($table_name, $list_id, $field, $obj);
        }
        
        /**
         * Detects if string end with provided string
         * 
         * @param string $haystack Full string
         * @param string $needle End part to be checked
         * @return boolean
         */
        private function endsWith($haystack, $needle)
        {
            $length = strlen($needle);
            if ($length == 0) {
                return true;
            }

            return (substr($haystack, -$length) === $needle);
        }
        
         /**
         * Pārbauda, vai norādīti obligātie lauki un vai reģistrs ar tādu nosaukumu neeksistē
         * 
         * @return void
         */
        private function validateData()
        {
            if ($this->obj_id == 0 || $this->register_title == '' || $this->form_title == '')
            {
                throw new Exceptions\DXCustomException("Visi lauki ir obligāti jānorāda!");
            }

            $list_row = DB::table('dx_lists')->where('object_id', '=', $this->obj_id)->where('list_title', '=', $this->register_title)->first();

            if ($list_row)
            {
                throw new Exceptions\DXCustomException("Reģistrs ar nosaukumu '" . $this->register_title . "' jau eksistē!");
            }
        }

    }

}
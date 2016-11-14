<?php

namespace App\Libraries\Structure
{

    use Input;
    use DB;
    use App\Exceptions;    
    
    /**
     * Generates view for existing list - including all fields defined for list
     */
    class StructMethod_view_generate extends StructMethod
    {        
        
        public $list_id = 0;
        public $view_title = "";
        
        public $exclude_fields = ['created_user_id', 'created_time', 'modified_user_id', 'modified_time'];
        
        /**
         * Init params
         * 
         * @return void
         */
        public function initData()
        {
            $this->list_id = Input::get('list_id', 0);
            
            if ($this->list_id == 0)
            {
                $this->list_id = Input::get('item_id', 0);
            }
            
            $this->view_title = Input::get('view_title', '');
        }
        
        /**
         * Returs HTML form for view generation
         * 
         * @return string HTML form
         */

        public function getFormHTML()
        {
            return view('structure.view_generate', [
                        'form_guid' => $this->form_guid,
                        'list_id' => $this->list_id
                    ])->render();
        }

        /**
         * Generates view
         * 
         * @return void
         */

        public function doMethod()
        {
            $this->validateData();
            
            DB::transaction(function () {
                
                $view_id = DB::table('dx_views')->insertGetId(
                    array('list_id' => $this->list_id, 'title' => $this->view_title, 'view_type_id' => 1, 'is_default' => 0)
                );
                
                $counter = 1;
                $fields = DB::table('dx_lists_fields as lf')
                          ->select('lf.id')
                          ->leftJoin('dx_forms_fields as ff', 'lf.id', '=', 'ff.field_id')
                          ->leftJoin('dx_forms_tabs as ft', 'ff.tab_id', '=', 'ft.id')
                          ->where('lf.list_id', '=', $this->list_id)
                          ->whereNotIn('lf.db_name', $this->exclude_fields)
                          ->orderBy('ft.order_index')
                          ->orderBy('ff.order_index')
                          ->get();
                
                foreach($fields as $field)
                {
                        DB::table('dx_views_fields')->insert([
                            'list_id' => $this->list_id,
                            'view_id' => $view_id,
                            'field_id' => $field->id,
                            'width' => 100,
                            'order_index' => $counter*10,
                            'is_item_link' => 1
                        ]);

                        $counter++;
                }
            });
        }
        
         /**
         * Pārbauda, vai norādīti obligātie lauki un vai reģistrs ar tādu nosaukumu neeksistē
         * 
         * @return void
         */
        private function validateData()
        {
            if ($this->list_id == 0 || $this->view_title == '')
            {
                throw new Exceptions\DXCustomException("Visi lauki ir obligāti jānorāda!");
            }

            $view_row = DB::table('dx_views')->where('id', '=', $this->list_id)->where('title', '=', $this->view_title)->first();

            if ($view_row)
            {
                throw new Exceptions\DXCustomException("Skats ar nosaukumu '" . $this->view_title . "' jau eksistē!");
            }
        }

    }

}
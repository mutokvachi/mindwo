<?php

namespace App\Libraries\Structure
{
    use Input;
    use DB;
    use App\Exceptions;
    use Auth;
    use App\Libraries\DBHistory;
    
    class StructMethod_view_copy extends StructMethod
    {
        /**
          *
          * Skata kopēšanas klase
          *
          *
          * Objekts nodrošina skata kopēšanu no cita skata
          *
         */

        /**
         * Kopējamā skata ID
         * 
         * @var integer
         */
        private $view_id = 0;
        
        /**
         * Jaunā skata nosaukums
         * 
         * @var string
         */
        private $view_title = "";

        /**
         * Kopējamā skata reģistra ID
         * 
         * @var integer
         */
        private $list_id = 0;
        
        /**
         * Kopējamā skata datu masīvs
         * 
         * @var Array 
         */
        private $view_row = null;        
        
        /**
         * Inicializē klases parametrus
         * 
         * @return void
         */

        public function initData()
        {
            $this->view_id = Input::get('view_id', 0); // ja POST formu uz ģenerēšanu

            $this->list_id = Input::get('item_id', 0); // ja izsauc formas atrādīšanu ar AJAX            
            
            $this->view_title = Input::get('view_title', ''); // ja POST formu uz ģenerēšanu
        }

        /**
         * Atgriež skata kopēšanas uzstādījumu HTML formu
         * 
         * @return string HTML forma
         */

        public function getFormHTML()
        {
            $views_list = $this->getObjTable('dx_views');
            
            return view('structure.view_copy', [
                        'form_guid' => $this->form_guid,
                        'views' => $this->getViews(),
                        'views_list_id' => $views_list->list_id
                    ])->render();
        }

        /**
         * Izveido skatu
         * 
         * @return void
         */

        public function doMethod()
        {
            $this->validateData();

            DB::transaction(function ()
            {
                $this->copyView();
            });
        }

         /**
         * Izgūst visus reģistra skatus
         * 
         * @return Array Masīvs ar reģistra skatiem
         */

        private function getViews()
        {
            $views = DB::table('dx_views')->where('list_id', '=', $this->list_id)->orderBy('title', 'ASC')->get();

            if (count($views) == 0)
            {
                throw new Exceptions\DXCustomException("Reģistram nav definēts neviens skats, kuru varētu kopēt!");
            }

            return $views;
        }
        
        /**
         * Pārbauda, vai norādīti obligātie lauki un vai reģistrs ar tādu nosaukumu neeksistē
         * 
         * @return void
         */

        private function validateData()
        {
            if ($this->view_title == '' || $this->view_id == 0)
            {
                throw new Exceptions\DXCustomException("Ir jānorāda skata nosaukums vai skats, no kura tiks veikta kopēšana!");
            }
            
            $this->view_row = DB::table('dx_views')->where('id', '=',$this->view_id)->first();
            
            if (!$this->view_row) {
                throw new Exceptions\DXCustomException("Skats ar ID '" . $this->view_id . "' neeksistē!");
            }
            
            $this->list_id = $this->view_row->list_id;
        }

        /**
         * Izveido datu bāzes skata ieraksta kopiju
         * 
         * @param Array      $obj_fields    Oriģinālā skata masīvs ar laukiem
         * @param string     $title         Jaunā skata nosaukums
         * @return integer                  Jaunā skata ID
         */

        private function getNewViewID($obj_fields, $title)
        {
            $obj_table = 'dx_views';
            
            $flds = array();
            foreach ($obj_fields as $key => $val)
            {
                if ($key != "id" && $key != "title" && $key != 'url' && $key != "created_user_id" && $key != "created_time" && $key != "modified_user_id" && $key != "modified_time")
                {
                    $flds[$key] = $val;
                }
            }
            $flds['title'] = $title;
            $flds['is_default'] = 0;
            $flds['created_user_id'] = Auth::user()->id;
            $flds['created_time'] = date('Y-n-d H:i:s');
            
            $new_id = DB::table($obj_table)->insertGetId($flds);
            
            $view_table = $this->getObjTable($obj_table);
            
            $history = new DBHistory($view_table, null, null, $new_id);
            $history->makeInsertHistory();
            
            return $new_id;
        }

        /**
         * Kopē norādītā skata lauku
         * 
         * @param            $flds_table            Lauku tabulas objekts, izmantojams vēstures veidošanai
         * @param integer    $new_view_id           Jaunā skata ID
         * @param Array      $o_field               Masīvs ar kopējamā skata lauka atribūtiem
         * @return void
         */

        private function copyViewFields($flds_table, $new_view_id,  $o_field)
        {
            $flds = array();
            foreach ($o_field as $key => $val)
            {
                if ($key != "id" && $key != 'view_id' && $key != "list_id" && $key != "created_user_id" && $key != "created_time" && $key != "modified_user_id" && $key != "modified_time")
                {
                    $flds[$key] = $val;
                }

            }
            
            $flds['view_id'] = $new_view_id;
            $flds['list_id'] = $this->list_id;
            $flds['created_user_id'] = Auth::user()->id;
            $flds['created_time'] = date('Y-n-d H:i:s');
            
            $fld_id = DB::table('dx_views_fields')->insertGetId($flds);
            $history = new DBHistory($flds_table, null, null, $fld_id);
            $history->makeInsertHistory();
        }

        /**
         * Kopē skatu un tā laukus
         * 
         * @return void
         */

        private function copyView()
        {            
            $new_view_id = $this->getNewViewID($this->view_row, $this->view_title);

            $view_fields = DB::table('dx_views_fields')->where('view_id', '=', $this->view_id)->get();
            
            $flds_table = $this->getObjTable('dx_views_fields');
            
            foreach ($view_fields as $v_field)
            {
                $this->copyViewFields($flds_table, $new_view_id, $v_field);
            }
            
        }
    }

}
<?php
namespace App\Libraries\Blocks 
{    
   
    use Request;
    use Input;
    use Auth;
    use DB;
    use Config;
    use Log;
    
    use App\Exceptions;    
    
    /**
     * Reģistru skats katalogu veidā 
     */
    class Block_FOLDERS extends Block 
    {              
        /**
         * Attēlojamā menu ID
         * 
         * @var integer 
         */
        private $menu_id = 0;
        
        /**
         * Masīvs ar katalogiem
         * 
         * @var type 
         */
        private $sets = [];
                
        /**
        * Izgūst bloka HTML
        * 
        * @return string Bloka HTML
        */
        public function getHTML()
        {            
            return  view('blocks.folders', [
                         'menu_id' => $this->menu_id,
                         'sets' => $this->sets
                    ])->render();            
        }
        
        /**
        * Izgūst bloka JavaScript
        * 
        * @return string Bloka JavaScript loģika
        */
        public function getJS()
        {
            return "";             
        }
        
        /**
        * Izgūst bloka CSS
        * 
        * @return string Bloka CSS
        */
        public function getCSS() 
        {
            return  view('blocks.folders_css')->render();  
        }

        /**
         * Izgūst bloka JSON datus
         * 
         * @return string Bloka JSON dati
         */
        public function getJSONData()
        {
            return "";
        }
        
        /**
        * Izgūst bloka parametra vērtības
        * Parametrus norāda lapas HTML teksta veidā speciālos simbolos [OBJ=...|VIEW_ID=...]
        * 
        * @return void
        */
        protected function parseParams()
        {            
            $this->menu_id = Input::get('menu_id', 0);
            
            $this->fillIncludesArr();
            
            $this->sets = $this->fillMenuItems(0, 0);
        }
        
        /**
         * Aizpilda masīvu ar JavaScript iekļāvumiem
         */
        private function fillIncludesArr()
        {
            
            if (Request::ajax())
            {
                return;
            }
            
             // Katalogu funkcionalitāte
            $this->addJSInclude('js/blocks/folders.js');
                        
        }
        
        private function fillMenuItems($parent_id, $level) {
            $items = $this->getMenuRows($parent_id);
                        
            $arr_items = [];
            $arr_sets = [];
            
            foreach($items as $item) {
                $rez = $this->fillMenuItems($item->id, $level+1);
                
                if (count($rez) > 0) {                    
                    $item->is_register = 0;
                    $item->href = '';
                    
                    foreach($rez as $set) {
                        array_push($arr_sets, $set);
                    }
                    $item->item_count = count($rez);
                }
                else if ($item->list_id > 0) {
                    $item->href = Request::root() . '/skats_' . $item->view_url;
                    $item->is_register = 1;
                    $item->item_count = 0;
                }
                
                if (isset($item->is_register)) {
                    array_push($arr_items, $item);
                }
            }
            
            if (count($arr_items) > 0) {
                array_push($arr_sets, ['parent_id' => $parent_id, 'items' => $arr_items]);
            }
            
            //Log::info("Līmenis: " . $level . " vērtības: " . json_encode($arr_sets));
            
            return $arr_sets;
        }
        
        private function getMenuRows($parent_id) {
            return  DB::table('dx_menu as m')
                    ->leftJoin('dx_views as v', function($join) {
                        $join->on('m.list_id', '=', 'v.list_id')
                             ->where('v.is_default', '=', 1);                        
                    })
                    ->leftJoin('dx_lists as l', 'm.list_id', '=', 'l.id')
                    ->select('m.id', 'm.title', 'm.list_id', 'm.fa_icon', DB::raw('ifnull(v.url, v.id) as view_url'), 'l.list_title')
                    ->where(function($query) {
                        $query->whereNull('m.list_id')
                              ->orWhere(function($query_or) {
                                  $query_or->whereIn('m.list_id', function($query_in) {
                                      $query_in->select('rl.list_id')
                                               ->from('dx_users_roles as ur')
                                               ->join('dx_roles_lists as rl', 'ur.role_id', '=', 'rl.role_id')
                                               ->where('ur.user_id', '=', Auth::user()->id)
                                               ->distinct();
                                  });
                              });
                    })
                    ->where(function($query) use ($parent_id) {
                        if ($parent_id > 0) {
                            $query->where('m.parent_id', '=', $parent_id);
                        }
                        else {
                            $query->whereNull('m.parent_id');
                        }
                    })
                    ->where(function($query) {
                        $query->whereNull('m.group_id')
                              ->orWhere('m.group_id', '=', Config::get('dx.menu_group_id', 0));
                    })
                    ->orderBy('m.order_index')
                    ->get();
        }
        
    }
}
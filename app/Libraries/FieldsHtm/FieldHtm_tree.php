<?php

namespace App\Libraries\FieldsHtm
{
    use DB;
    use PDO;
    
    /**
     * Hierarhisku ierakstu (koka) lauka attēlošanas klase
     */
    class FieldHtm_tree extends FieldHtm
    {
        /**
         * Pilnais ceļš līdz elementam sākot no vecākā, piemēram, el1->el11->el111 utt
         * Izmanto attēlošanai formas laukā
         * 
         * @var string
         */
        private $tree_full_path = "";
        
        /**
         * Atgriež lauka attēlošanas HTML
         */
        public function getHtm()
        {
            $is_fld_disabled = ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode;            
                        
            $rows = $this->getTreeRows();
            
            $tree = $this->generatePageTree($rows);
            
            return view('fields.tree', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->fld_attr->db_name, 
                    'item_value' => $this->item_value,
                    'item_full_path' => $this->tree_full_path,
                    'is_disabled' => $is_fld_disabled,
                    'form_title' => trans('fields.tree_choose_title'),
                    'is_shown_param' => str_replace("-", "_", $this->frm_uniq_id),
                    'tree' => ($is_fld_disabled) ? '' : $tree
            ])->render(); 
        }
        
        /**
         * Returns textual value of the field
         */
        public function getTxtVal()
        {
            $rows = $this->getTreeRows();
            
            $tree = $this->generatePageTree($rows);
            
            return $this->tree_full_path;
        }

        /**
         * Uzstāda noklusēto vērtību jauna ieraksta gadījumā
         */
        protected function setDefaultVal()
        {
            if ($this->item_id == 0 && strlen($this->fld_attr->default_value) > 0)
            {
                $this->item_value = $this->fld_attr->default_value;
            }
        }
        
        /**
         * Iegūst masīvu ar koka elementiem
         */
        private function getTreeRows() {
            $sql_multi = "";
            if ($this->fld_attr->is_multi_registers==1)
            {
                    $sql_multi = " AND multi_list_id = " . $this->fld_attr->rel_list_id;
            }
                
            $sql_rel = "SELECT id, " . $this->fld_attr->rel_parent_field_name . " as parent_id, " . $this->fld_attr->rel_field_name . " as title FROM " . $this->fld_attr->rel_table_name . " WHERE 1=1" . $sql_multi . " ORDER BY " . $this->fld_attr->rel_field_name;
                        
            DB::setFetchMode(PDO::FETCH_ASSOC); // We need to get values as array to use it in recursion                  
                    
            $rows = DB::select($sql_rel);                 
                    
            DB::setFetchMode(PDO::FETCH_CLASS); // Set back default fetch mode
            
            return $rows;
        }
        
        /**
         * Rekursīvi uzzīmē kokveida struktūru un atgriež attiecīgo HTML
         * @param Array     $datas      Masīvs ar elementiem
         * @param integer   $parent     Vecāka elementa ID
         * @param integer   $depth      Pašreizējais līmenis
         * @param string    $full_path  Pilnais ceļš uz elementu, formātā el1->el2->el3...
         * @return string Kokveida struktūras HTMLs
         */
        private function generatePageTree($datas, $parent = 0, $depth=0, $full_path = ""){
            
            if($depth > 1000){
                return ''; // Make sure not to have an endless recursion
            }
            
            $tree = '<ul>';
                        
            if (strlen($full_path) > 0)
            {
                $full_path .= "->";
            }
            
            for($i=0, $ni=count($datas); $i < $ni; $i++){
                if($datas[$i]['parent_id'] == $parent){                    
                    
                    $node_path = $full_path . $datas[$i]['title'];
                    
                    if ($this->item_value == $datas[$i]['id'])
                    {
                        $this->tree_full_path = $node_path;
                    }
                    
                    $tree .= view('elements.tree_node', [
                        'node_path' => $node_path, 
                        'node_id' => $datas[$i]['id'], 
                        'node_title' => $datas[$i]['title'],
                        'node_children' => $this->generatePageTree($datas, $datas[$i]['id'], $depth+1, $node_path)
                    ])->render();                    
                }
            }
            
            $tree .= '</ul>';
            return $tree;
        } 

    }

}
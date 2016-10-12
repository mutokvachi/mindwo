<?php

namespace App\Libraries\FieldsSave
{
    use DB;
    use \App\Exceptions;
    use Log;
    
    class FieldSave_rel_id extends FieldSave
    {
        /**
         *
         * Formas relāciju lauka klase
         * Objekts nodrošina formas lauka vērtību apstrādi relāciju tipa laukiem
         */
        
        /**
         * Apstrādā lauka vērtību
         */
        public function prepareVal()
        {
            $id = $this->request->input($this->fld->db_name, 0);
            
            if ($id == 0)
            {
                $id = null;
            }
            else
            {                
                $this->validateDataSource($id);
                $this->is_val_set = 1;
            }
            
            $this->val_arr[$this->fld->db_name] = $id;
            
            if ($this->fld->is_fields_synchro) {
                $this->synchroRelField($id);
            }
        }
        
        /**
         * Sinhronizē saistītā ieraksta reģistra lauku (abpusēji saistīto ierakstu gadījumā, piemēram, saņemtais un atbildes dokumenti)
         * 
         * @param integer $id Saistītā ieraksta ID
         * @throws Exceptions\DXCustomException
         */
        private function synchroRelField($id) {
            
            if ($this->item_id == 0 && $id == null) {
                return;
            }
            
            $fld_rel = DB::table('dx_lists_fields')
                   ->where('list_id', '=', $this->fld->rel_list_id)
                   ->where('db_name', '=', $this->fld->db_name)
                   ->where('rel_list_id', '=', $this->fld->list_id)
                   ->first();
            
            if (!$fld_rel) {
                throw new Exceptions\DXCustomException("Laukam " . $this->fld->db_name . " nav atrodams sinhronizējamais saistītā reģistra lauks! Sazinieties ar sistēmas uzturētāju.");
            }
            
            if ($this->item_id == 0) {
                array_push($this->upd_rel_arr, array('table' => $this->fld->rel_table_name, 'id' => $id, 'field' => $fld_rel->db_name, 'oper' => 'update'));
                return;
            }
            
            $cur_val = DB::table($this->fld->table_name)
                       ->select($this->fld->db_name . " as rel_val")
                       ->where('id', '=', $this->item_id)
                       ->first();
            
            if ($cur_val->rel_val != null && $cur_val->rel_val != $id) {               
                array_push($this->upd_rel_arr, array('table' => $this->fld->rel_table_name, 'id' => $cur_val->rel_val, 'field' => $fld_rel->db_name, 'oper' => 'null'));
            }
            
            if ($id != null) {
                array_push($this->upd_rel_arr, array('table' => $this->fld->rel_table_name, 'id' => $id, 'field' => $fld_rel->db_name, 'oper' => 'update'));
            }
            
            
            /*
            DB::table($this->fld->rel_table_name)
            ->where('id', '=', $id)
            ->update([$fld_rel->db_name => $this->item_id]);
             * 
             */
        }

    }

}
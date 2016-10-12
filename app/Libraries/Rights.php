<?php
namespace App\Libraries 
{
    use DB;
    use Auth;
    use \Illuminate\Support\Facades\Schema;
    use App\Libraries\Workflows;
    
    /**
     * Klase nodrošina tiesību kontroli
     */
    class Rights
    { 
        /**
         * Nosaka rediģēšanas tiesības (iespēju) uz reģistra ierakstu
         * 
         * @param integer $list_id Reģistra ID
         * @param integer $item_id Ieraksta ID
         * @return boolean Atgriež True, ja lietotājs drīkst un var rediģēt ierakstu, False pretējā gadījumā
         */
        public static function getIsEditRightsOnItem($list_id, $item_id)
        {
            if ($item_id == 0)
            {
                return false; // item is not editable because it is not created/saved jet
            }
            
            $doc_table = Workflows\Helper::getListTableName($list_id);
            
            if(!Schema::hasColumn($doc_table, 'dx_item_status_id')) {
                return true; // reģistra tabulā nav lauks dx_item_status_id, bez kura nav iespējamas darbplūsmas, tāpēc ierakstu var rediģēt
            }
                    
            $cur_task = Workflows\Helper::getCurrentTask($list_id, $item_id);

            if ($cur_task)
            {
                if ($cur_task->task_type_id == \App\Http\Controllers\TasksController::TASK_TYPE_FILL_ACCEPT && Auth::user()->id == $cur_task->task_employee_id)
                {
                    return true; // workflow is running and current user have doc edit task
                }
                else
                {
                    return false; // workflow is running and current uses does not have edit task                        
                }
            }

            $item_data = DB::table($doc_table)
                         ->where("id","=", $item_id)
                         ->where("dx_item_status_id", "=", \App\Http\Controllers\TasksController::WORKFLOW_STATUS_APPROVED)
                         ->first();

            if ($item_data)
            {
                return false; // Dokuments ir apstiprināts, ierakstu nevar rediģēt
            }
            else
            {
                return true;
            }
        }
        
        public static function getRightsOnList($list_id)
        {
            $rez = null;

            $sql = "
            select
                *
            from
            (
            select 
                max(rl.is_delete_rights) as is_delete_rights,
                max(rl.is_edit_rights) as is_edit_rights,
                max(rl.is_new_rights) as is_new_rights
            from 
                dx_users_roles ur 
                inner join dx_roles_lists rl on ur.role_id = rl.role_id 
            where 
                ur.user_id = :user_id
                AND rl.list_id = :list_id
            ) tb
            where
                tb.is_delete_rights is not null
            limit 0, 1
            ";

            $rights = DB::select($sql, array('user_id' => Auth::user()->id, 'list_id' => $list_id));

            if (count($rights) > 0)
            {
                $rez = $rights[0];
            }

            return $rez;
        }
        
        public static function getRightsOnPage($page_id)
        {
            $rez = null;

            $sql = "
            select 
                1 as is_right
            from 
                dx_users_roles ur 
                inner join dx_roles_pages rp on ur.role_id = rp.role_id 
            where 
                ur.user_id = :user_id
                AND rp.page_id = :page_id
            limit 0,1
            ";

            $rights = DB::select($sql, array('user_id' => Auth::user()->id, 'page_id' => $page_id));

            if (count($rights) > 0)
            {
                
                $rez = $rights[0];
            }

            return $rez;
        }  
        
        /**
         * Nosaka lietotāja tiesības atbilstoši datu avotam. Ja lietotājam nav norādīts neviens datu avots, tad tiesības uz visiem datu avotiem.
         * Ja reģistrā ir definēta kolonna "Datu avots", tad lietotājam pieejami tikai ieraksti atbilstoši lietotāja datu avotam
         * 
         * @param integer $list_id      Reģistra ID, kurā jāpārbauda tiesības uz datu avotu
         * @param string $table_name    Tabulas nosaukums, kurā glabājas reģistra dati
         * @return string SQL WHERE nosacījums atticībā uz datu avotu
         */
        public static function getSQLSourceRights($list_id, $table_name) {
            
            if (!Auth::user()->source_id) {
                return; // lietotājam piekļuve visiem datu avotiem
            }
            
            $fld_row = DB::table('dx_lists_fields')
                        ->where('list_id', '=', $list_id)
                        ->where('db_name','=','source_id')
                        ->first();
            
            $source_where = " ";
            
            if ($fld_row) {
                $source_where = " AND " . $table_name . ".source_id = " . Auth::user()->source_id . " ";
            }
            
            
            if ($table_name == "in_sources") {
                $source_where = " AND " . $table_name . ".id = " . Auth::user()->source_id . " ";
            }
            
            return $source_where;
        }
    }
}

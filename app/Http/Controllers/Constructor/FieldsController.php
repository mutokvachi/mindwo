<?php

namespace App\Http\Controllers\Constructor;

use App\Http\Controllers\Controller;
use App\Libraries\Rights;
use DB;
use App\Exceptions;

/**
 * Registers UI fields controller
 */
class FieldsController extends Controller
{	
    /**
     * Returns all available field names for given register by field type
     * @param integer $list_id Register ID
     * @param integer $field_type_id Field type ID
     */
    public function getDBFields($list_id, $field_type_id)
    {
        $this->checkRights();
        
        $obj = DB::table('dx_objects as o')
               ->select('o.db_name as table_name')
               ->join('dx_lists as l', 'o.id', '=', 'l.object_id')
               ->where('l.id', '=', $list_id)
               ->first();
        
        $used = DB::table('dx_lists_fields')
                ->select('db_name')
                ->where('list_id', '=', $list_id)
                ->get();
        
        $arr_used = [];
        foreach($used as $used_fld) {
            array_push($arr_used, $used_fld->db_name);
        }
        
        $data = DB::table('dx_tables_fields as tf')
                ->where('tf.table_name', '=', $obj->table_name)
                ->where('tf.field_type_id', '=', $field_type_id)
                ->whereNotIn('tf.field_name', $arr_used)
                ->orderBy('tf.field_name')
                ->get();
        
        return response()->json(['success' => 1, 'rows' => json_encode($data), 'count' => count($data)]);
    }
	
    /**
     * Check user rights on list
     * 
     * @param type $list_id
     * @throws Exceptions\DXCustomException
     */
    private function checkRights() {
        
        $list_id = \App\Libraries\DBHelper::getListByTable('dx_tables_fields')->id;
        
        $rights = Rights::getRightsOnList($list_id);

        if ($rights == null) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_register'));
        }
    }
}

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
        $this->checkRights($list_id);
        
        return response()->json(['success' => 1, 'rows' => json_encode($data)]);
    }
	
    /**
     * Check user rights on list
     * 
     * @param type $list_id
     * @throws Exceptions\DXCustomException
     */
    private function checkRights($list_id) {
        
        $rights = Rights::getRightsOnList($list_id);

        if ($rights == null) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_register'));
        }
    }
}

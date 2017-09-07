<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Request;
use App\Libraries\Rights;
use DB;
use App\Exceptions; 

/**
 * Core UI controller for administrators
 */
class CoreController extends Controller
{    
    /**
     * Returns all lookup fields available for given register (trying to get from lookup view first)
     * 
     * @param integer $list_id Register ID
     * @return \Illuminate\Http\JsonResponse Available fields in JSON format
     */  
    public function getLookupFields($list_id) {
        
        $this->checkRights($list_id);
        
        $view_row = getLookupViewRow($list_id);
        
        $fields = DB::table('dx_views_fields as vf')
                  ->select('lf.id', 'lf.title_list')
                  ->join('dx_lists_fields as lf', 'vf.field_id', '=', 'lf.id')
                  ->where('vf.view_id', '=', $view_row->id)
                  ->where('lf.db_name', '!=', 'id')
                  ->orderBy('lf.title_list')
                  ->get();
                   
        
        return response()->json(['success' => 1, 'fields' => json_encode($fields)]);
    }    
    
    /**
     * Check rights on list
     * 
     * @param integer $list_id List ID
     * @throws Exceptions\DXCustomException
     */
    private function checkRights($list_id) {
                
        // Let's check user rights on list
        $rights = Rights::getRightsOnList($list_id);

        if ($rights == null) {
            \Log::info("No rights on lookup register with ID: " . $list_id);
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_register'));
        }
    }

}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Request;
use App\Libraries\Rights;
use DB;
use App\Exceptions; 

/**
 * API class for views operations
 */
class ViewController extends Controller
{
    /**
     * View data row (from table dx_views)     * 
     * @var object 
     */
    private $view_row = null;
    
    /**
     * Returns all view data in JSON format
     * 
     * @param mixed $view_id View ID (from db table dx_views.id) or view name (dx_views.url)
     * @return \Illuminate\Http\JsonResponse Views data in JSON format
     * @throws Exceptions\DXCustomException
     */
    public function getAllData($view_id) {
        
        $this->setClassParams($view_id);
        
        $view = new \App\Libraries\DataView\DataViewJSON($this->view_row->id, "", "");
        
        return response()->json(['success' => 1, 'rows' => $view->getViewHtml()]);
    }
    
     /**
     * Returns filtered view data in JSON format
     * Filtering can be done by one field
     * 
     * @param mixed $view_id View ID (from db table dx_views.id) or view name (dx_views.url)
     * @param mixed $field Field ID (from db table dx_lists_fields.id) or field name (dx_lists_fields.db_name)
     * @param mixed $criteria Filtering criteria (some ID value or text) - will be used SQL operator LIKE
     * @return \Illuminate\Http\JsonResponse Views data in JSON format
     * @throws Exceptions\DXCustomException
     */
    public function getFilteredData($view_id, $field, $criteria) {
        
        $this->setClassParams($view_id);
        
        $field_row = $this->getFieldRow($field);
        
        $filter_data = json_encode([[
            0 => "", 
            1 => $criteria,
            2 => $field_row->id
        ]]);        
        
        $view = new \App\Libraries\DataView\DataViewJSON($this->view_row->id, $filter_data, "");
        
        return response()->json(['success' => 1, 'rows' => $view->getViewHtml()]);
    }
    
    /**
     * Inits class parameters - finds view row in database and checks user rights on list
     * 
     * @param mixed $view_id View ID
     * @throws Exceptions\DXCustomException
     */
    private function setClassParams($view_id) {
        $this->view_row = getViewRowByID(Request::url(), $view_id);
        
        // Let's check user rights on list
        $rights = Rights::getRightsOnList($this->view_row->list_id);

        if ($rights == null) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_register'));
        }
    }
    
    /**
     * Gets field row by field ID or field name
     * 
     * @param mixed $field Field ID (db table dx_lists_fields.id) or field name (db table dx_lists_fields.db_name)
     * @return Object Field row (from table dx_lists_fields)
     * @throws Exceptions\DXCustomException
     */
    private function getFieldRow($field) {
                
        if (is_numeric($field))
        {
            $fld = "id";
        }
        else
        {
            $fld = "db_name";
        }
        
        $field_row = DB::table('dx_lists_fields')
                    ->where($fld, '=', $field)
                    ->where('list_id', '=', $this->view_row->list_id)
                    ->first();

        if (!$field_row)
        {
            throw new Exceptions\DXCustomException(sprintf(trans('errors.field_not_found'), $field));
        }

        return $field_row;
    
    }

}

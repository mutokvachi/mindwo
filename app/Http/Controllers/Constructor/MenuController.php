<?php

namespace App\Http\Controllers\Constructor;

use App\Http\Controllers\Controller;
use App\Libraries\Rights;
use DB;
use App\Exceptions;
use mindwo\pages\Menu;
use Illuminate\Http\Request;
use App\Libraries\DBHistory;
use Auth;

/**
 * Menu builder UI controller
 */
class MenuController extends Controller
{
    /**
     * Array where menu items will be stored and indexed
     * @var array 
     */
    private $arr_items = [];
    
    /**
     * List ID for menu table dx_menu
     * 
     * @var integer
     */
    private $list_id = 0;
    
    /**
     * Returns menu builder page
     */
    public function getMenuBuilderPage($site_id)
    {
        $this->checkRights();
        $sites = DB::table('dx_menu_groups')->orderBy('title')->get();
        
        if (!$site_id) {
            $site_id = $sites[0]->id;
        }
        
        $menu = new Menu(1, $site_id);
        
        return view('constructor.menu.page', [
            'step' => 'names',
            'menu' => $menu->getHTML(),
            'sites_items' => $sites,
            'site_id' => $site_id
        ]);
    }
	
    /**
     * Updates menu items hierarchy and order
     * 
     * @param integer $site_id Site ID (from table dx_menu_groups)
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse Saving status in JSON
     */
    public function updateMenu($site_id, Request $request) {
        $this->checkRights();
        
        $this->fillItems(json_decode($request->input('items', [])), 0);
        $site = DB::table("dx_menu_groups")->where("id", '=', $site_id)->first();
        
        $list_object = \App\Libraries\DBHelper::getListObject($this->list_id);
        $list_object->table_name = $list_object->db_name; // in order to work history logic for updates

        $list_fields = DBHistory::getListFields($this->list_id);
        
        DB::transaction(function () use ($site, $list_object, $list_fields){
            foreach($this->arr_items as $itm) {
                
                $arr_data = [
                    ":parent_id" => intval($itm["parent_id"]) ? $itm["parent_id"] : null,
                    ":order_index" => $itm["order_index"],
                ];                        
                        
                $history = new DBHistory($list_object, $list_fields, $arr_data, $itm["id"]);
                $history->compareChanges();
                $history->makeUpdateHistory();

                if ($history->is_update_change) {
                    
                    DB::table("dx_menu")
                    ->where('id', '=', $itm["id"])
                    ->where(function($query) use ($itm, $site) {
                        $query->where("parent_id", "!=", $itm["parent_id"])
                              ->orWhere("order_index", "!=", $itm["order_index"])
                              ->orWhere("title_index", "!=", DB::raw("CONCAT('" . $site->title . ": [" . sprintf("%04d", $itm["order_index"]) . "] ', dx_menu.title)"));
                    })
                    ->update([
                        "parent_id" => intval($itm["parent_id"]) ? $itm["parent_id"] : null,
                        "order_index" => $itm["order_index"],
                        "title_index" => DB::raw("CONCAT('" . $site->title . ": [" . sprintf("%04d", $itm["order_index"]) . "] ', dx_menu.title)"),
                        "modified_user_id" => Auth::user()->id,
                        "modified_time" => date('Y-n-d H:i:s')
                    ]);
                }
            }
        });
        
        return response()->json(['success' => 1]);        
    }
    
    /**
     * Prepares array with menu items to be saved/updated
     * 
     * @param Array $items JSON array with menu items
     * @param integer $parent_id Menu item parent ID
     */
    private function fillItems($items, $parent_id) {
        $idx = 0;
        
        foreach($items as $itm) {
            $idx = $idx + 10;
            
            array_push($this->arr_items, [
                'id' => $itm->id,
                'order_index' => $idx,
                'parent_id' => $parent_id
            ]);
            
            if (isset($itm->children)) {
                $this->fillItems($itm->children, $itm->id);
            }
        }
    }
   
    /**
     * Check user rights on list for table dx_menu
     * 
     * @param type $list_id
     * @throws Exceptions\DXCustomException
     */
    private function checkRights() {
        
        $this->list_id = \App\Libraries\DBHelper::getListByTable('dx_menu')->id;
        
        $rights = Rights::getRightsOnList($this->list_id);

        if ($rights == null) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_register'));
        }
    }
}

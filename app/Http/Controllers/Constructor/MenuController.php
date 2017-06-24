<?php

namespace App\Http\Controllers\Constructor;

use App\Http\Controllers\Controller;
use App\Libraries\Rights;
use DB;
use App\Exceptions;
use mindwo\pages\Menu;
use Illuminate\Http\Request;

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
	
    public function updateMenu($site_id, Request $request) {
        $this->checkRights();
        
        $this->fillItems(json_decode($request->input('items', [])), 0);
        $site = DB::table("dx_menu_groups")->where("id", '=', $site_id)->first();
        
        DB::transaction(function () use ($site){
            foreach($this->arr_items as $itm) {
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
                    "title_index" => DB::raw("CONCAT('" . $site->title . ": [" . sprintf("%04d", $itm["order_index"]) . "] ', dx_menu.title)")
                ]);
            }
        });
        
        return response()->json(['success' => 1]);        
    }
    
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
        
        $list_id = \App\Libraries\DBHelper::getListByTable('dx_menu')->id;
        
        $rights = Rights::getRightsOnList($list_id);

        if ($rights == null) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_register'));
        }
    }
}

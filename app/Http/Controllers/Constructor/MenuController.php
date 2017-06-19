<?php

namespace App\Http\Controllers\Constructor;

use App\Http\Controllers\Controller;
use App\Libraries\Rights;
use DB;
use App\Exceptions;

/**
 * Menu builder UI controller
 */
class MenuController extends Controller
{	
    /**
     * Returns menu builder page
     */
    public function getMenuBuilderPage()
    {
        $this->checkRights();
        
        return view('constructor.menu.page', [
            'step' => 'names',
        ]);
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

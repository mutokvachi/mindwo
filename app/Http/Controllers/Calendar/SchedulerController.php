<?php

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\Controller;
use App\Libraries\Rights;
use DB;
use App\Exceptions;
use mindwo\pages\Menu;
use Illuminate\Http\Request;
use App\Libraries\DBHistory;
use Auth;

/**
 * Scheduler UI controller
 */
class SchedulerController extends Controller
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
     * Returns scheduler page
     */
    public function getSchedulerPage()
    {
        $this->checkRights();
        
        
        return view('calendar.scheduler.page', [
            'groups_list_id' => $this->list_id,
            'subjects' => DB::table('edu_subjects')->orderBy('title')->get()
        ]);
    }
    
    /**
     * Check user rights on list for table edu_subjects_groups
     * 
     * @param type $list_id
     * @throws Exceptions\DXCustomException
     */
    private function checkRights() {
        
        $this->list_id = \App\Libraries\DBHelper::getListByTable('edu_subjects')->id;
        
        $rights = Rights::getRightsOnList($this->list_id);

        if ($rights == null) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_register'));
        }
    }
}

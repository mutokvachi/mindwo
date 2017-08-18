<?php

namespace App\Http\Controllers\Calendar;

use App\Http\Controllers\Controller;
use App\Libraries\Rights;
use DB;
use App\Exceptions;
use Illuminate\Http\Request;
use App\Libraries\DBHistory;
use Auth;
use stdClass;

/**
 * Scheduled groups validation and publishing controller
 */
class PublishController extends Controller
{
    /**
     * Validates and publish provided groups
     * 
     * @param \Illuminate\Http\Request $request GET request
     * @return \Illuminate\Http\JsonResponse Returns validation errors array and success status in JSON
     */
    public function publishGroups(Request $request)
    {
        $this->checkRights();
        
        return response()->json([
            'success' => 1,/* 
            'subjects' => json_encode($this->getSubjects()),
            'groups' => json_encode($this->getGroups()),
            'rooms' => json_encode($rooms),
            'rooms_cbo' => json_encode($this->getCboRooms($rooms))             
             */
        ]);
    }
    
   
    
    /**
     * Check user rights on list for table edu_subjects_groups
     * 
     * @param type $list_id
     * @throws Exceptions\DXCustomException
     */
    private function checkRights() {
        
        //$subjects_list_id = \App\Libraries\DBHelper::getListByTable('edu_subjects')->id;
        
        $groups_list_id = \App\Libraries\DBHelper::getListByTable('edu_subjects_groups')->id;
        /*
        $this->days_list_id = \App\Libraries\DBHelper::getListByTable('edu_subjects_groups_days')->id;
        $this->rooms_list_id = \App\Libraries\DBHelper::getListByTable('edu_rooms')->id;
        $this->coffee_list_id = \App\Libraries\DBHelper::getListByTable('edu_subjects_groups_days_pauses')->id;
        */
        
        $rights = Rights::getRightsOnList($groups_list_id);

        if ($rights == null) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_register'));
        }
    }
}

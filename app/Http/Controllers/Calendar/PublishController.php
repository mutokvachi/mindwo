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
        $this->validate($request, [
            'groups_ids' => 'required'
        ]);
        
        $this->checkRights();
        
        /*
        Visām nodarbībām ir norādīts vismaz viens pasniedzējs;
        Visām grupām ir norādīta vismaz viena nodarbība;
        Ja kādai nodarbībai vairāki pasniedzēji, tad nepārklājās pasniedzēju laiki;
        Grupai norādītais mācību pasākums, modulis un programma ir publicēti;
        Grupas vietu limits nepārsniedz vietu limitu telpās, kurās notiek nodarbības;
        Nepārklājās dažādu grupu nodarbību laiki kādā no telpām;
        Nepārklājās grupas nodarbības laiks telpā, kura tiek izmantota tajā pašā dienā kafijas pauzēm;
        Visām kafijas pauzēm ir norādīti pakalpojumu sniedzēji;
        Grupās, kurās dalībnieki paši nevar pieteikties (tikai ar uzaicinājumu), dalībnieku kopējais skaits pa uzaicināmajām iestādēm ir vienāds ar grupu vietu limitu;
        Grupās, kurās dalībnieki paši nevar pieteikties (tikai ar uzaicinājumu), ir aizpildītas vismaz 50% vietas;
        Grupās, kurās dalībnieki paši nevar pieteikties (tikai ar uzaicinājumu), dalībnieku skaits nepārsniedz grupas vietu limitu;
         */
        
        return response()->json([
            'success' => 1,
            'err_count' => 4,
            'err_htm' => view('calendar.scheduler.err_groups')->render()
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

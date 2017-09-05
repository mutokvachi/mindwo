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
use App\Libraries\DB_DX;
use App\Jobs\SendNotifyMail;
use App\Libraries\Workflows;

/**
 * Scheduled groups validation and publishing controller
 */
class PublishController extends Controller
{
    private $portal_url = null;

    private $portal_name = null;

    /**
     *  Validates and publish provided groups
     
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
        Uz komplektēšanu var nodot tikai iekšējās grupas;
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
        $mode = $request->input('mode', 'publish');
        
        $fld_where = ($mode == "publish") ? "is_for_publish" : "is_for_complect";
        
        $validators = DB::table('edu_publish_validators')
                      ->where($fld_where, '=', true)
                      ->get();
        
        $groups = explode(",", $request->input('groups_ids'));
        $arr_groups = [];
        
        foreach($groups as $group) {
            $group_row = DB::table('edu_subjects_groups')
                         ->where('id', '=', $group)
                         ->first();
            
            if (!$group_row) {
                throw new Exceptions\DXCustomException(trans('errors.publish_validator_no_group', ['id' => $group]));
            }
            
            foreach($validators as $validator) {
                $valid = Validators\ValidatorFactory::build_validator($validator->code, $group_row);
                
                $err_arr = $valid->getErrors();
                
                if (count($err_arr)) {
                    
                    if (!isset($arr_groups[$group])) {
                        $arr_groups[$group]['errors'] = [];
                        $arr_groups[$group]['group_id'] = $group_row->id;
                        $arr_groups[$group]['group_title'] = $group_row->title;
                    }
                    
                    foreach($err_arr as $err) {
                        array_push($arr_groups[$group]['errors'], $err);
                    }
                }
            }
        }
        
        if ($request->input("is_publish", 0) && count($arr_groups) == 0) {            
            
            $members = [];
            $members_templ = null;

            if ($mode == "publish") {
                $arr_vals = [
                    'is_published' => 1,
                    'first_publish' => date('Y-n-d H:i:s'),
                    'is_complecting' => 0
                ];

                $members_templ = DB::table('dx_emails_templates')->where('code', '=', "MEMBERS_PUBLISH")->first();
                if ($members_templ) {
                    $members = $this->getOrgsMembers($groups);
                }
            }
            else {
                $arr_vals = [
                    'is_complecting' => 1
                ];
            }

            $coord_templ = DB::table('dx_emails_templates')->where('code', '=', "COORD_" . strtoupper($mode))->first();
            
            $coord = [];
            if ($coord_templ) {
                $coord = $this->getOrgsCoord($groups);
            }

            $dx_db = (new DB_DX())->table('edu_subjects_groups');
            $arr_dbs = [];
            foreach($groups as $group) {
            
                array_push($arr_dbs, 
                    $dx_db
                        ->where('id', '=', $group)
                        ->update($arr_vals)
                );
            }
    
            $email_list_id = \App\Libraries\DBHelper::getListByTable("dx_emails_sent")->id;

            DB::transaction(function () use ($arr_dbs, $coord, $coord_templ, $mode, $members, $members_templ, $email_list_id){
                foreach($arr_dbs as $db) {
                    $db->commitUpdate();
                }

                if (count($coord)) {
                    foreach($coord as $cor) {

                        $mail_text = str_replace("\${Content}", view('emails.education.coord_' . $mode, ['groups' => $cor['groups']]), $coord_templ->mail_text);
                        
                        $email_id = DB::table('dx_emails_sent')->insertGetId([
                            'template_id' => $coord_templ->id,
                            'mail_subject' => $coord_templ->mail_subject,
                            'mail_text' => $mail_text,
                            'user_id' => $cor['user_id']
                        ]);

                        $this->sendMail($cor['email'],  $coord_templ->mail_subject, $mail_text, $email_id);

                        $infoTask = new Workflows\InfoTask($email_list_id, $email_id, false);
                        $infoTask->makeTask($cor['user_id'], null, "Jāveic komplektēšana mācību grupām. Komplektējamo grupu skaits: " . count($cor['groups']));                
                    
                    }
                }

                if (count($members)) {
                    foreach($members as $mem) {
                        
                        $mail_text = str_replace("\${Content}", view('emails.education.members_publish', ['days' => $mem['days']]), $members_templ->mail_text);
                        
                        $email_id = DB::table('dx_emails_sent')->insertGetId([
                            'template_id' => $members_templ->id,
                            'mail_subject' => $members_templ->mail_subject,
                            'mail_text' => $mail_text,
                            'user_id' => $mem['user_id']
                        ]);

                        $this->sendMail($mem['email'],  $members_templ->mail_subject, $mail_text, $email_id);
                            
                        $infoTask = new Workflows\InfoTask($email_list_id, $email_id, false);
                        $infoTask->makeTask($mem['user_id'], null, "Jums ir pieejami jauni mācību pasākumi. Pasākumu skaits: " . count($mem['days']));                

                    }
                }
            });
            
        }
                
        return response()->json([
            'success' => 1,
            'err_count' => count($arr_groups),
            'err_htm' => view('calendar.scheduler.err_groups', [
                            'groups' => $arr_groups
                         ])->render()
        ]);
    }

    private function getOrgsMembers($groups) {
        $notify = DB::table('edu_subjects_groups_members as gm')
                ->select(                     
                    DB::raw('ifnull(ou.email, u.email) as email'),
                    'd.lesson_date',
                    'd.time_from',
                    'd.time_to',
                    'ou.user_id',
                    's.title as title_subject',
                    DB::raw('ifnull(r.room_address, o.address) as room_address'),
                    'r.room_nr',
                    'o.title as title_org'
                )               
                ->join('edu_subjects_groups as g', 'gm.group_id', '=', 'g.id')
                ->join('edu_subjects as s', 'g.subject_id', '=', 's.id')
                ->join('edu_orgs_users as ou', 'gm.org_id', '=', 'ou.org_id')
                ->join('dx_users as u', 'ou.user_id', '=', 'u.id')
                ->join('edu_subjects_groups_days as d', 'd.group_id', '=', 'g.id')
                ->join('edu_rooms as r', 'd.room_id', '=', 'r.id')
                ->join('edu_orgs as o', 'r.org_id', '=', 'o.id')
                ->whereIn('gm.group_id', $groups)
                ->orderBy('lesson_date')
                ->get();

        $arr_info = [];
        foreach($notify as $info) {

            if (!isset($arr_info[$info->user_id])) {
                $arr_info[$info->user_id] = [
                    'user_id' => $info->user_id,
                    'email' => $info->email,
                    'days' => []
                ];
            }

            array_push($arr_info[$info->user_id]['days'], 
                [
                    'lesson_date' => $info->lesson_date,
                    'time_from' => substr($info->time_from, 0, 5),
                    'time_to' => substr($info->time_to, 0, 5),                    
                    'title_subject' => $info->title_subject,
                    'title_org' => $info->title_org,
                    'room_address' => $info->room_address,
                    'room_nr' => $info->room_nr
                ]
            );
        }

        return $arr_info;
        
    }
    
    private function getOrgsCoord($groups) {
        $notify = DB::table('edu_subjects_groups_orgs as go')
               ->select(
                   'o.title as title_org',
                   DB::raw('ifnull(ou.email, u.email) as email'),
                   'go.places_quota',
                   DB::raw('(SELECT min(d.lesson_date) FROM edu_subjects_groups_days as d WHERE d.group_id = go.group_id) as date_from'),
                   DB::raw('(SELECT max(d.lesson_date) FROM edu_subjects_groups_days as d WHERE d.group_id = go.group_id) as date_to'),
                   'ou.user_id',
                   's.title as title_subject',
                   DB::raw('(SELECT count(*) FROM edu_subjects_groups_members as m WHERE m.group_id = go.group_id AND m.org_id = ou.org_id) as empl_count')
               )
               ->join('edu_subjects_groups as g', 'go.group_id', '=', 'g.id')
               ->join('edu_subjects as s', 'g.subject_id', '=', 's.id')
               ->join('edu_orgs as o', 'go.org_id', '=', 'o.id')
               ->join('edu_orgs_users as ou', 'go.org_id', '=', 'ou.org_id')
               ->join('dx_users as u', 'ou.user_id', '=', 'u.id')              
               ->where('u.is_role_coordin', '=', 1)
               ->whereIn('go.group_id', $groups)
               ->orderBy('date_from')
               ->get();
        
        $arr_info = [];
        foreach($notify as $info) {

            if (!isset($arr_info[$info->user_id])) {
                $arr_info[$info->user_id] = [
                    'user_id' => $info->user_id,
                    'email' => $info->email,
                    'groups' => []
                ];
            }

            array_push($arr_info[$info->user_id]['groups'], 
                [
                    'title_org' => $info->title_org,
                    'places_quota' => $info->places_quota,
                    'date_from' => $info->date_from,
                    'date_to' => $info->date_to,
                    'title_subject' => $info->title_subject,
                    'empl_count' => $info->empl_count
                ]
            );
        }

        return $arr_info;
    }
    
    private function getPortalName() {
        if (!$this->portal_name) {
            $this->portal_name =  get_portal_config("PORTAL_NAME");
        }

        return $this->portal_name;
    }

    private function getPortalUrl() {
        if (!$this->portal_url) {
            $this->portal_url =  get_portal_config("PORTAL_PUBLIC_URL");
        }

        return $this->portal_url;
    }

    private function sendMail($email, $subject, $content, $email_id) {
        
        $arr_data = [
            'email' => $email,
            'subject' => $subject,
            'content' => $content,
            "portal_url" => $this->getPortalUrl(),
            "portal_name" => $this->getPortalName(),
            'email_id' => $email_id
        ];
        
        $this->dispatch(new SendNotifyMail($arr_data));
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

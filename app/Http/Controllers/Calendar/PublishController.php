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
    /**
     * Portal URL - used in emails
     *
     * @var string
     */
    private $portal_url = null;

    /**
     * Portal name - used in emails
     *
     * @var string
     */
    private $portal_name = null;

    /**
     * Organization coordinators email template - row from dx_emails_templates
     *
     * @var array
     */
    private $coord_templ = null;

    /**
     * Groups members email template - row from dx_emails_templates
     *
     * @var array
     */
    private $members_templ = null;

    /**
     * Teachers email template - row from dx_emails_templates
     *
     * @var array
     */
    private $teachers_templ = null;

    /**
     * Coffee pauses provider email template - row from dx_emails_templates
     *
     * @var array
     */
    private $coffee_templ = null;

    /**
     * Sent emails register ID from table dx_lists
     *
     * @var integer
     */
    private $email_list_id = 0;

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
        $groups = explode(",", $request->input('groups_ids'));
        
        $arr_err = $this->validateGroups($groups, $mode);
        
        if ($request->input("is_publish", 0) && count($arr_err) == 0) {
            $this->publishInfoNotify($groups, $mode);            
        }
                
        return response()->json([
            'success' => 1,
            'err_count' => count($arr_err),
            'err_htm' => view('calendar.scheduler.err_groups', [
                            'groups' => $arr_err
                         ])->render()
        ]);
    }

    /**
     * Stores published info in db and sends out notifications
     *
     * @param array $groups Array with groups IDs to be validated
     * @param string $mode Action mode - "publish" or "complect"
     * @return void
     */
    private function publishInfoNotify($groups, $mode) {
        // prepare arrays with data and templates according to mode
        $arr_vals = $this->prepareModeData($mode);

        // prepare arrays with notifications receivers
        $teachers = $this->getTeachers($groups);
        $coord = $this->getOrgsCoord($groups);
        $members = $this->getOrgsMembers($groups);
        $coffee = $this->getCoffeeStuff($groups);

        // prepare update statements for groups
        $dx_db = (new DB_DX())->table('edu_subjects_groups');
        $arr_dbs = [];
        foreach($groups as $group) {
        
            array_push($arr_dbs, 
                $dx_db
                    ->where('id', '=', $group)
                    ->update($arr_vals)
            );
        }        

        // save groups data and send notifications
        DB::transaction(function () use ($mode, $arr_dbs, $coord, $members, $teachers, $coffee){
            foreach($arr_dbs as $db) {
                $db->commitUpdate();
            }

            $this->notifyCoordinators($mode, $coord);            
            $this->notifyMembers($members);
            $this->notifyTeachers($teachers);
            $this->notifyCoffeeStuff($coffee);
        });
    }

    /**
     * Prepares publish/complecting fields array for update and sets class parameters depending on mode
     * Sets email templates
     *
     * @param string $mode Action mode - "publish" or "complect"
     * @return array Array with fields values (statuses) to be updated in db table edu_subjects_groups
     */
    private function prepareModeData($mode) {
        
        $this->coord_templ = DB::table('dx_emails_templates')->where('code', '=', "COORD_" . strtoupper($mode))->first();        

        $this->email_list_id = \App\Libraries\DBHelper::getListByTable("dx_emails_sent")->id;

        if ($mode == "publish") {
            $arr_vals = [
                'is_published' => 1,
                'first_publish' => date('Y-n-d H:i:s'),
                'is_complecting' => 0
            ];

            $this->members_templ = DB::table('dx_emails_templates')->where('code', '=', "MEMBERS_PUBLISH")->first();
            $this->teachers_templ = DB::table('dx_emails_templates')->where('code', '=', "TEACHERS_PUBLISH")->first();
            $this->coffee_templ = DB::table('dx_emails_templates')->where('code', '=', "COFFEE_PUBLISH")->first();            
        }
        else {
            $arr_vals = [
                'is_complecting' => 1
            ];
        }

        return $arr_vals;
    }

    /**
     * Send notifications to organizations coordinators
     *
     * @param string $mode Action mode - "publish" or "complect"
     * @param array $coord Array with coordinators
     * @return void
     */
    private function notifyCoordinators($mode, $coord) {
        foreach($coord as $cor) {

            $mail_text = str_replace("\${Content}", view('emails.education.coord_' . $mode, ['groups' => $cor['groups']]), $this->coord_templ->mail_text);
            
            $email_id = DB::table('dx_emails_sent')->insertGetId([
                'template_id' => $this->coord_templ->id,
                'mail_subject' => $this->coord_templ->mail_subject,
                'mail_text' => $mail_text,
                'user_id' => $cor['user_id']
            ]);

            $this->sendMail($cor['email'],  $this->coord_templ->mail_subject, $mail_text, $email_id);

            $infoTask = new Workflows\InfoTask($this->email_list_id, $email_id, false);
            $infoTask->makeTask($cor['user_id'], null, "Jāveic komplektēšana mācību grupām. Komplektējamo grupu skaits: " . count($cor['groups']));                        
        }
    }

    /**
     * Send notifications to groups members about published groups
     *
     * @param array $coord Array with coordinators
     * @return void
     */
    private function notifyMembers($members) {
        foreach($members as $mem) {
            
            $mail_text = str_replace("\${Content}", view('emails.education.members_publish', ['days' => $mem['days']]), $this->members_templ->mail_text);
            
            $email_id = DB::table('dx_emails_sent')->insertGetId([
                'template_id' => $this->members_templ->id,
                'mail_subject' => $this->members_templ->mail_subject,
                'mail_text' => $mail_text,
                'user_id' => $mem['user_id']
            ]);

            $this->sendMail($mem['email'],  $this->members_templ->mail_subject, $mail_text, $email_id);
                
            $infoTask = new Workflows\InfoTask($this->email_list_id, $email_id, false);
            $infoTask->makeTask($mem['user_id'], null, "Jums ir pieejami jauni mācību pasākumi. Pasākumu skaits: " . count($mem['days']));
        }
    }

    /**
     * Send notifications to groups members about published groups
     *
     * @param array $coord Array with coordinators
     * @return void
     */
    private function notifyTeachers($teachers) {
        foreach($teachers as $teac) {
            
            $mail_text = str_replace("\${Content}", view('emails.education.teachers_publish', ['days' => $teac['days']]), $this->teachers_templ->mail_text);
            
            $email_id = DB::table('dx_emails_sent')->insertGetId([
                'template_id' => $this->teachers_templ->id,
                'mail_subject' => $this->teachers_templ->mail_subject,
                'mail_text' => $mail_text,
                'user_id' => $teac['user_id']
            ]);

            $this->sendMail($teac['email'],  $this->teachers_templ->mail_subject, $mail_text, $email_id);
                
            $infoTask = new Workflows\InfoTask($this->email_list_id, $email_id, false);
            $infoTask->makeTask($teac['user_id'], null, "Jums ir ieplānots pasniegt jaunus mācību pasākumus. Pasākumu skaits: " . count($teac['days']));
        }
    }

    /**
     * Send notifications to groups coffee pauses stuff about published groups
     *
     * @param array $coord Array with coffee pauses stuff
     * @return void
     */
    private function notifyCoffeeStuff($coffee) {
        foreach($coffee as $teac) {
            
            $mail_text = str_replace("\${Content}", view('emails.education.teachers_publish', ['days' => $teac['days']]), $this->coffee_templ->mail_text);
            
            $email_id = DB::table('dx_emails_sent')->insertGetId([
                'template_id' => $this->coffee_templ->id,
                'mail_subject' => $this->coffee_templ->mail_subject,
                'mail_text' => $mail_text,
                'user_id' => $teac['user_id']
            ]);

            $this->sendMail($teac['email'],  $this->coffee_templ->mail_subject, $mail_text, $email_id);
                
            $infoTask = new Workflows\InfoTask($this->email_list_id, $email_id, false);
            $infoTask->makeTask($teac['user_id'], null, "Jums ir jānodrošina kafijas pauzes jauniem pasākumiem. Pasākumu skaits: " . count($teac['days']));
        }
    }

    /**
     * Validate groups against vaildations stored in db table edu_publish_validators
     *
     * @param array $groups Array with groups IDs to be validated
     * @param string $mode Action mode - "publish" or "complect"
     * @return array Returns array with validation errors if any
     */
    private function validateGroups($groups, $mode) {
        $fld_where = ($mode == "publish") ? "is_for_publish" : "is_for_complect";
        
        $validators = DB::table('edu_publish_validators')
                      ->where($fld_where, '=', true)
                      ->get();

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

        return $arr_groups;
    }

    /**
     * Return array with groups teachers which will be notified regarding publishing
     *
     * @param array $groups Groups IDs to be published
     * @return array Array with teachers info
     */
    private function getTeachers($groups) {
        
        if (!$this->teachers_templ) {
            return [];
        }

        $notify = DB::table('edu_subjects_groups_days_teachers as dt')
                ->select(                     
                    'u.email',
                    'gd.lesson_date',
                    'dt.time_from',
                    'dt.time_to',
                    'u.id as user_id',
                    's.title as title_subject',
                    DB::raw('ifnull(r.room_address, o.address) as room_address'),
                    'r.room_nr',
                    'o.title as title_org',
                    'gd.group_id'
                )               
                ->join('edu_subjects_groups_days as gd', 'dt.group_day_id', '=', 'gd.id')
                ->join('edu_subjects_groups as g', 'gd.group_id', '=', 'g.id')
                ->join('edu_subjects as s', 'g.subject_id', '=', 's.id')                
                ->join('dx_users as u', 'dt.teacher_id', '=', 'u.id')
                ->join('edu_rooms as r', 'gd.room_id', '=', 'r.id')
                ->join('edu_orgs as o', 'r.org_id', '=', 'o.id')
                ->whereIn('gd.group_id', $groups)
                ->orderBy('gd.lesson_date')
                ->orderBy('dt.time_from')
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

            $members_count = DB::table('edu_subjects_groups_members')->where('group_id', '=', $info->group_id)->count();
            array_push($arr_info[$info->user_id]['days'], 
                [
                    'lesson_date' => $info->lesson_date,
                    'time_from' => substr($info->time_from, 0, 5),
                    'time_to' => substr($info->time_to, 0, 5),                    
                    'title_subject' => $info->title_subject,
                    'title_org' => $info->title_org,
                    'room_address' => $info->room_address,
                    'room_nr' => $info->room_nr,
                    'members_count' => $members_count
                ]
            );
        }

        return $arr_info;        
    }

    /**
     * Return array with groups coffee providers which will be notified regarding publishing
     *
     * @param array $groups Groups IDs to be published
     * @return array Array with coffee pauses providers info
     */
    private function getCoffeeStuff($groups) {
        
        if (!$this->coffee_templ) {
            return [];
        }

        $notify = DB::table('edu_subjects_groups_days_pauses as dp')
                ->select(                     
                    DB::raw('ifnull(pou.email, u.email) as email'),
                    'gd.lesson_date',
                    'dp.time_from',
                    'dp.time_to',
                    'u.id as user_id',
                    's.title as title_subject',
                    DB::raw('ifnull(r.room_address, o.address) as room_address'),
                    'r.room_nr',
                    'o.title as title_org',
                    'gd.group_id'
                )               
                ->join('edu_subjects_groups_days as gd', 'dp.group_day_id', '=', 'gd.id')
                ->join('edu_subjects_groups as g', 'gd.group_id', '=', 'g.id')
                ->join('edu_subjects as s', 'g.subject_id', '=', 's.id') 
                ->join('edu_orgs as po', 'dp.feed_org_id', '=', 'po.id')
                ->join('edu_orgs_users as pou', 'po.id', '=', 'pou.org_id')               
                ->join('dx_users as u', 'pou.user_id', '=', 'u.id')
                ->join('edu_rooms as r', 'gd.room_id', '=', 'r.id')
                ->join('edu_orgs as o', 'r.org_id', '=', 'o.id')
                ->whereRaw('(pou.end_date is null or pou.end_date >date(now()))')
                ->whereIn('gd.group_id', $groups)
                ->orderBy('gd.lesson_date')
                ->orderBy('dp.time_from')
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

            //ToDo: add teachers count
            //ToDo: add another stuff count (add in UI interface too seperate tab)
            $members_count = DB::table('edu_subjects_groups_members')->where('group_id', '=', $info->group_id)->count();
            array_push($arr_info[$info->user_id]['days'], 
                [
                    'lesson_date' => $info->lesson_date,
                    'time_from' => substr($info->time_from, 0, 5),
                    'time_to' => substr($info->time_to, 0, 5),                    
                    'title_subject' => $info->title_subject,
                    'title_org' => $info->title_org,
                    'room_address' => $info->room_address,
                    'room_nr' => $info->room_nr,
                    'members_count' => $members_count
                ]
            );
        }

        return $arr_info;        
    }

    /**
     * Return array with groups members which will be notified regarding publishing
     *
     * @param array $groups Groups to be published
     * @return array Array with members info
     */
    private function getOrgsMembers($groups) {

        if (!$this->members_templ) {
            return [];
        }

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
    
    /**
     * Returns array with organizations coordinators which will be notified regarding publish/complect
     *
     * @param array $groups array with gorups IDs to be published/complected
     * @return array Array with coordinators info
     */
    private function getOrgsCoord($groups) {

        if (!$this->coord_templ) {
            return [];
        }

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
    
    /**
     * Return portal name from dx_config table
     *
     * @return string Portal name
     */
    private function getPortalName() {
        if (!$this->portal_name) {
            $this->portal_name =  get_portal_config("PORTAL_NAME");
        }

        return $this->portal_name;
    }

    /**
     * Return portal URL from dx_config table
     *
     * @return string Portal URL
     */
    private function getPortalUrl() {
        if (!$this->portal_url) {
            $this->portal_url =  get_portal_config("PORTAL_PUBLIC_URL");
        }

        return $this->portal_url;
    }

    /**
     * Send mail - put in into queue
     *
     * @param string $email Email address
     * @param string $subject Email subject
     * @param string $content Email content
     * @param integer $email_id Email ID, all outgoing emails are stored in table dx_emails_sent
     * @return void
     */
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

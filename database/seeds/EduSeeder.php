<?php

use Illuminate\Database\Seeder;

class EduSeeder extends Seeder
{  
    const ROLE_MAIN = 74;
    const ROLE_ORG = 75;
    const ROLE_TEACH = 76;
    const ROLE_STUD = 77;
    const ROLE_SUPPORT = 78;
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {    
        DB::transaction(function () {
            
            DB::table('edu_subjects_groups')->delete();
            DB::table('edu_orgs_users')->delete();
            DB::table('dx_users_roles')
                    ->whereIn('role_id', [self::ROLE_MAIN, self::ROLE_ORG, self::ROLE_TEACH, self::ROLE_STUD, self::ROLE_SUPPORT])
                    ->delete();
            
            DB::table('dx_roles_lists')
                    ->whereIn('role_id', [self::ROLE_MAIN, self::ROLE_ORG, self::ROLE_TEACH, self::ROLE_STUD, self::ROLE_SUPPORT])
                    ->delete();
            
            DB::table('dx_tasks')->delete();
            
            DB::table('dx_users_supervise')->delete();
            
            DB::table('dx_users')
                    ->where('id', '>', 2)->delete();
            
            DB::table('edu_rooms')->delete();
            DB::table('edu_orgs')->delete();
            
            DB::table('edu_subjects_groups_days')->delete();
            DB::table('edu_subjects_groups')->delete();
            DB::table('edu_subjects_teachers')->delete();
            DB::table('edu_subjects')->delete();
            DB::table('edu_modules')->delete();
            DB::table('edu_programms')->delete();
            
            DB::table('dx_supervise')->delete();
            
            // Supervise
            $sup1 = DB::table('dx_supervise')->insertGetId([
               'title' => 'Valsts adminisrācijas skola' 
            ]);
            
            // Organizations
            $main_org = DB::table('edu_orgs')->insertGetId([
                'title' => 'Valsts administrācijas skola',
                'org_type_id' => 1,
                'reg_nr' => '90000028156',
                'address' => 'Raiņa bulvāris 4, Rīga, LV-1050',
            ]);
            
            $rooms =  [
                [
                    'room_nr' => 'E-mācības',
                    'room_limit' => 0,
                    'org_id' => $main_org,
                    'is_elearn' => 1
                ],
            ];
            DB::table('edu_rooms')->insert($rooms);
                       
            // ---- End organizations
            
            // Main coordinators
            $main_user1 = DB::table('dx_users')->insertGetId([
                'first_name' => 'Kristaps',
                'last_name' => 'Kalns',
                'person_code' => rand('100000', '999999'),
                'is_role_coordin_main' => 1,
                'password' => '$2y$10$S803OIF2qhP8ZliilBZI9eYGCMXhhvAkiWuXXcG3J.Pp2hnImjv2q',
                'login_name' => 'kristaps.kalns@vas.gov.lv',
                'email' => 'kristaps.kalns@vas.gov.lv'
            ]);
            
            DB::table('edu_orgs_users')->insert([
                'user_id' => $main_user1,
                'org_id' => $main_org,
                'job_title' => 'IT vadītājs',
                'email' => 'kristaps.kalns@vas.gov.lv',
                'mobile' => '(+371) 26318965'
            ]);
            
            DB::table('dx_users_roles')->insert([
                'role_id' => self::ROLE_MAIN,
                'user_id' => $main_user1,
            ]);
            
            DB::table('dx_users_supervise')->insert([
               'user_id' => $main_user1,
               'supervise_id' => $sup1,
            ]);
            
            $main_user2 = DB::table('dx_users')->insertGetId([
                'first_name' => 'Gatis',
                'last_name' => 'Bērzinš',
                'person_code' => rand('100000', '999999'),
                'is_role_coordin_main' => 1,
                'password' => '$2y$10$S803OIF2qhP8ZliilBZI9eYGCMXhhvAkiWuXXcG3J.Pp2hnImjv2q',
                'login_name' => 'gatis.berzins@vas.gov.lv',
                'email' => 'gatis.berzins@vas.gov.lv'
            ]);
            
            DB::table('edu_orgs_users')->insert([
                'user_id' => $main_user2,
                'org_id' => $main_org,
                'job_title' => 'Mācību koordinators Eiropas fondu projektā U',
                'email' => 'gatis.berzins@vas.gov.lv',
                'phone' => '(+371) 67821286'
            ]);
            
            DB::table('dx_users_roles')->insert([
                'role_id' => self::ROLE_MAIN,
                'user_id' => $main_user2,
            ]);
            
            DB::table('dx_users_supervise')->insert([
               'user_id' => $main_user2,
               'supervise_id' => $sup1,
            ]);
            
            $main_user3 = DB::table('dx_users')->insertGetId([
                'first_name' => 'Sanita',
                'last_name' => 'Medne',
                'person_code' => rand('100000', '999999'),
                'is_role_coordin_main' => 1,
                'password' => '$2y$10$S803OIF2qhP8ZliilBZI9eYGCMXhhvAkiWuXXcG3J.Pp2hnImjv2q',
                'login_name' => 'sanita.medne@vas.gov.lv',
                'email' => 'sanita.medne@vas.gov.lv'
            ]);
            
            DB::table('edu_orgs_users')->insert([
                'user_id' => $main_user3,
                'org_id' => $main_org,
                'job_title' => 'Mācību koordinators Eiropas fondu projektā K',
                'email' => 'sanita.medne@vas.gov.lv',
                'phone' => '(+371) 67821285'
            ]);
            
            DB::table('dx_users_roles')->insert([
                'role_id' => self::ROLE_MAIN,
                'user_id' => $main_user3,
            ]);
            
            DB::table('dx_users_supervise')->insert([
               'user_id' => $main_user3,
               'supervise_id' => $sup1,
            ]);
            
            $main_user4 = DB::table('dx_users')->insertGetId([
                'first_name' => 'Līga',
                'last_name' => 'Griķe',
                'person_code' => rand('100000', '999999'),
                'is_role_coordin_main' => 1,
                'password' => '$2y$10$S803OIF2qhP8ZliilBZI9eYGCMXhhvAkiWuXXcG3J.Pp2hnImjv2q',
                'login_name' => 'liga.grike@vas.gov.lv',
                'email' => 'liga.grike@vas.gov.lv'
            ]);
            
            DB::table('edu_orgs_users')->insert([
                'user_id' => $main_user4,
                'org_id' => $main_org,
                'job_title' => 'Mācību koordinators VAS pamatapmācību departamentā',
                'email' => 'liga.grike@vas.gov.lv',
                'phone' => '(+371) 67229770'
            ]);
            
            DB::table('dx_users_roles')->insert([
                'role_id' => self::ROLE_MAIN,
                'user_id' => $main_user4,
            ]);
            
            DB::table('dx_users_supervise')->insert([
               'user_id' => $main_user4,
               'supervise_id' => $sup1,
            ]);
            // ---- End main coordinators
            
            // Programms and subjects
            $progr1 = DB::table('edu_programms')->insertGetId([
                'title' => 'U projekts',
                'code' => 'U',
                'is_published' => 1,
                'dx_supervise_id' => $sup1,
            ]);
            
            $progr2 = DB::table('edu_programms')->insertGetId([
                'title' => 'K projekts',
                'code' => 'K',
                'is_published' => 1,
                'dx_supervise_id' => $sup1,
            ]);
            
            $progr3 = DB::table('edu_programms')->insertGetId([
                'title' => 'Pamatdarbība',  
                'code' => 'P',
                'is_published' => 1,
                'dx_supervise_id' => $sup1,
            ]);
            
            $progr4 = DB::table('edu_programms')->insertGetId([
                'title' => 'Franču valoda',
                'code' => 'F',
                'is_published' => 1,
                'dx_supervise_id' => $sup1,
            ]);
            
            $this->rolesLists();
        });
    }   
    
    private function rolesLists() {
        $this->addRoleListFull(trans('db_dx_users.list_title_edu'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_dx_users.list_title_org'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_dx_users.list_title_teacher'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_dx_users.list_title_serv'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_dx_users.list_title_student'), self::ROLE_MAIN);  
        $this->addRoleListEditSelf(trans('db_dx_users.list_title_profile'), self::ROLE_MAIN, 'id');
        
        
        $this->addRoleListFull(trans('db_edu_certif.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_programms.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_modules.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_subjects.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull('Anketas un testi', self::ROLE_MAIN);
        
        // Classifiers
        $this->addRoleListFull(trans('db_edu_activities.list_name'), self::ROLE_MAIN);
        //$this->addRoleListRead(trans('db_dx_icons_files.list_name'), self::ROLE_MAIN);
        //$this->addRoleListRead(trans('db_in_tests_types.list_name'), self::ROLE_MAIN);
        //$this->addRoleListRead(trans('db_edu_subjects_types.list_name'), self::ROLE_MAIN);        
        //$this->addRoleListRead(trans('db_edu_orgs_types.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_dx_regions.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull('Numeratori', self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_banks.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_orgs.list_name'), self::ROLE_MAIN);        
        $this->addRoleListFull(trans('db_edu_orgs_banks.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_orgs_users.list_name'), self::ROLE_MAIN);
        
        $this->addRoleListFull(trans('db_edu_subjects_teachers.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_materials.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_subjects_materials.list_name'), self::ROLE_MAIN);
        
        
        $this->addRoleListFull(trans('db_edu_modules_activities.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_certif_templates.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_modules_students.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_modules_students_activities.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_subjects_groups.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_subjects_groups_members.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_rooms.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_subjects_groups_days.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_subjects_groups_attend.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_subjects_groups_days_teachers.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_subjects_groups_days_pauses.list_name'), self::ROLE_MAIN);        
        $this->addRoleListFull(trans('db_edu_rooms_calendars.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_tags.list_name'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_edu_subjects_tags.list_name'), self::ROLE_MAIN);
        
        $this->addRoleListFull(trans('db_dx_users.list_title_student'), self::ROLE_ORG);
        $this->addRoleListEditSelf(trans('db_dx_users.list_title_profile'), self::ROLE_ORG, 'id');
        //$this->addRoleListRead(trans('db_dx_users.list_title_all'), self::ROLE_ORG); // iespējams šo var ņemt ārā.., tas tikai lookupiem
        $this->addRoleListRead(trans('db_edu_orgs.list_name'), self::ROLE_ORG);
        $this->addRoleListFull(trans('db_edu_orgs_users.list_name'), self::ROLE_ORG);
        $this->addRoleListFull(trans('db_edu_certif.list_name'), self::ROLE_ORG);
        
        $this->addRoleListEditSelf(trans('db_dx_users.list_title_profile'), self::ROLE_TEACH, 'id');
        $this->addRoleListRead(trans('db_dx_users.list_title_all'), self::ROLE_TEACH);
        
        $this->addRoleListEditSelf(trans('db_dx_users.list_title_profile'), self::ROLE_STUD, 'id');
        $this->addRoleListReadSelf(trans('db_edu_certif.list_name'), self::ROLE_STUD, 'user_id');
        
        $this->addRoleListEditSelf(trans('db_dx_users.list_title_profile'), self::ROLE_SUPPORT, 'id');        
        
    }
    
    private function addRoleListFull($list_title, $role_id) {
        DB::table('dx_roles_lists')->insert([
            'role_id' => $role_id,
            'list_id' => DB::table('dx_lists')->where('list_title', '=', $list_title)->first()->id,
            'is_edit_rights' => 1,
            'is_delete_rights' => 1,
            'is_new_rights' => 1,
            'is_import_rights' => 0,
            'is_view_rights' => 0
        ]);
    }
    
    private function addRoleListEditSelf($list_title, $role_id, $user_field) {
        $list_id = DB::table('dx_lists')->where('list_title', '=', $list_title)->first()->id;
        $fld_id = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', $user_field)->first()->id;
        
        DB::table('dx_roles_lists')->insert([
            'role_id' => $role_id,
            'list_id' => $list_id,
            'is_edit_rights' => 1,
            'is_delete_rights' => 0,
            'is_new_rights' => 0,
            'is_import_rights' => 0,
            'is_view_rights' => 0,
            'user_field_id' => $fld_id,
        ]);
    }
    
    private function addRoleListReadSelf($list_title, $role_id, $user_field) {
        $list_id = DB::table('dx_lists')->where('list_title', '=', $list_title)->first()->id;
        $fld_id = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', $user_field)->first()->id;

        DB::table('dx_roles_lists')->insert([
            'role_id' => $role_id,
            'list_id' => $list_id,
            'is_edit_rights' => 0,
            'is_delete_rights' => 0,
            'is_new_rights' => 0,
            'is_import_rights' => 0,
            'is_view_rights' => 0,
            'user_field_id' => $fld_id,
        ]);
    }
    
    private function addRoleListRead($list_title, $role_id) {
        DB::table('dx_roles_lists')->insert([
            'role_id' => $role_id,
            'list_id' => DB::table('dx_lists')->where('list_title', '=', $list_title)->first()->id,
            'is_edit_rights' => 0,
            'is_delete_rights' => 0,
            'is_new_rights' => 0,
            'is_import_rights' => 0,
            'is_view_rights' => 0
        ]);
    }
   
}

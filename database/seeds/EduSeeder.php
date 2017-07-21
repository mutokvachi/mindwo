<?php

use Illuminate\Database\Seeder;

class EduSeeder extends Seeder
{  
    const ROLE_MAIN = 74;
    const ROLE_ORG = 75;
    const ROLE_TEACH = 76;
    const ROLE_STUD = 77;
    
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
                    ->whereIn('role_id', [self::ROLE_MAIN, self::ROLE_ORG, self::ROLE_TEACH])
                    ->delete();
            
            DB::table('dx_roles_lists')
                    ->whereIn('role_id', [self::ROLE_MAIN, self::ROLE_ORG, self::ROLE_TEACH])
                    ->delete();
            
            DB::table('dx_users')
                    ->where('id', '>', 2)->delete();
            
            // Organizations
            $main_org = DB::table('edu_orgs')->insertGetId([
                'title' => 'Valsts administrācijas skola',
                'org_type_id' => 1,
                'reg_nr' => '123456788',
                'address' => 'Raiņa bulvāris 4',
            ]);
            
            $stud_org = DB::table('edu_orgs')->insertGetId([
                'title' => 'Finanšu ministrija',
                'org_type_id' => 1,
                'reg_nr' => '2212121212',
                'address' => 'Aspazijas bulvāris 4',
            ]);
            
            $stud_org2 = DB::table('edu_orgs')->insertGetId([
                'title' => 'Labklājības ministrija',
                'org_type_id' => 1,
                'reg_nr' => '444343434',
                'address' => 'Aspazijas bulvāris 12',
            ]);
            
            $teach_org = DB::table('edu_orgs')->insertGetId([
                'title' => 'LATVICON mācību centrs',
                'org_type_id' => 1,
                'reg_nr' => '12121212',
                'address' => 'Brīvības bulvāris 4',
            ]);            
            // ---- End organizations
            
            // Main coordinators
            $main_user1 = DB::table('dx_users')->insertGetId([
                'first_name' => 'Oskars',
                'last_name' => 'Bērziņš',
                'person_code' => rand('100000', '999999'),
                'is_role_coordin_main' => 1,
                'password' => '$2y$10$S803OIF2qhP8ZliilBZI9eYGCMXhhvAkiWuXXcG3J.Pp2hnImjv2q',
                'login_name' => 'oskars',
            ]);
            
            DB::table('edu_orgs_users')->insert([
                'user_id' => $main_user1,
                'org_id' => $main_org,
                'job_title' => 'Konsultants',
            ]);
            
            DB::table('dx_users_roles')->insert([
                'role_id' => self::ROLE_MAIN,
                'user_id' => $main_user1,
            ]);
            // ---- End main coordinators
            
            // Orgs coordinators
            $org_user1 = DB::table('dx_users')->insertGetId([
                'first_name' => 'Māris',
                'last_name' => 'Ozoliņš',
                'person_code' => rand('100000', '999999'),
                'is_role_coordin' => 1,
                'password' => '$2y$10$S803OIF2qhP8ZliilBZI9eYGCMXhhvAkiWuXXcG3J.Pp2hnImjv2q',
                'login_name' => 'maris',
            ]);
            
            DB::table('edu_orgs_users')->insert([
                'user_id' => $org_user1,
                'org_id' => $stud_org,
                'job_title' => 'Iestādes mācību koordinators',
            ]);
            
            DB::table('dx_users_roles')->insert([
                'role_id' => self::ROLE_ORG,
                'user_id' => $org_user1,
            ]);
            // ---- End org coordinators
            
            // Teachers
            $teach1 = DB::table('dx_users')->insertGetId([
                'first_name' => 'Jānis',
                'last_name' => 'Kalniņš',
                'person_code' => rand('100000', '999999'),
                'is_role_teacher' => 1,
                'password' => '$2y$10$S803OIF2qhP8ZliilBZI9eYGCMXhhvAkiWuXXcG3J.Pp2hnImjv2q',
                'login_name' => 'janis',
            ]);
            
            DB::table('edu_orgs_users')->insert([
                'user_id' => $teach1,
                'org_id' => $teach_org,
                'job_title' => 'Ekonomikas pasniedzējs',
            ]);
            
            DB::table('dx_users_roles')->insert([
                'role_id' => self::ROLE_TEACH,
                'user_id' => $teach1,
            ]);
            
            $teach2 = DB::table('dx_users')->insertGetId([
                'first_name' => 'Aija',
                'last_name' => 'Bērziņa',
                'person_code' => rand('100000', '999999'),
                'is_role_teacher' => 1,
                'password' => '$2y$10$S803OIF2qhP8ZliilBZI9eYGCMXhhvAkiWuXXcG3J.Pp2hnImjv2q',
                'login_name' => 'aija',
            ]);
            
            DB::table('edu_orgs_users')->insert([
                'user_id' => $teach2,
                'org_id' => $teach_org,
                'job_title' => 'Matemātikas pasniedzējs',
            ]);
            
            DB::table('dx_users_roles')->insert([
                'role_id' => self::ROLE_TEACH,
                'user_id' => $teach2,
            ]);
            // ---- End teachers
            
            // Students
            $student1 = DB::table('dx_users')->insertGetId([
                'first_name' => 'Mērija',
                'last_name' => 'Kociņa',
                'person_code' => rand('100000', '999999'),
                'is_role_student' => 1,
                'password' => '$2y$10$S803OIF2qhP8ZliilBZI9eYGCMXhhvAkiWuXXcG3J.Pp2hnImjv2q',
                'login_name' => 'merija',
            ]);
            
            DB::table('edu_orgs_users')->insert([
                'user_id' => $student1,
                'org_id' => $stud_org,
                'job_title' => 'Lietvede',
            ]);
            
            DB::table('dx_users_roles')->insert([
                'role_id' => self::ROLE_STUD,
                'user_id' => $student1,
            ]);
            
            $student2 = DB::table('dx_users')->insertGetId([
                'first_name' => 'Maija',
                'last_name' => 'Evere',
                'person_code' => rand('100000', '999999'),
                'is_role_student' => 1,
                'password' => '$2y$10$S803OIF2qhP8ZliilBZI9eYGCMXhhvAkiWuXXcG3J.Pp2hnImjv2q',
                'login_name' => 'maija',
            ]);
            
            DB::table('edu_orgs_users')->insert([
                'user_id' => $student2,
                'org_id' => $stud_org,
                'job_title' => 'Galvenā grāmatvede',
            ]);
            
            DB::table('dx_users_roles')->insert([
                'role_id' => self::ROLE_STUD,
                'user_id' => $student2,
            ]);
            
            $student3 = DB::table('dx_users')->insertGetId([
                'first_name' => 'Līvija',
                'last_name' => 'Kalniņa',
                'person_code' => rand('100000', '999999'),
                'is_role_student' => 1,
                'password' => '$2y$10$S803OIF2qhP8ZliilBZI9eYGCMXhhvAkiWuXXcG3J.Pp2hnImjv2q',
                'login_name' => 'livija',
            ]);
            
            DB::table('edu_orgs_users')->insert([
                'user_id' => $student3,
                'org_id' => $stud_org2,
                'job_title' => 'Galvenā ekonomiste',
            ]);
            
            DB::table('dx_users_roles')->insert([
                'role_id' => self::ROLE_STUD,
                'user_id' => $student3,
            ]);
            // ---- End students
            
            // Programms and subjects
            $progr1 = DB::table('edu_programms')->insertGetId([
                'title' => 'Valsts pārvaldes juridiskie jautājumi',
                'sub_title' => "C modulis",
                'is_published' => 1,
            ]);
            
            $progr2 = DB::table('edu_programms')->insertGetId([
                'title' => 'Vadītāju attīstības programma',
                'sub_title' => "Demo apakšvirsraksts - iespējams nav tāds lauks nepieciešams..",
                'is_published' => 1,
            ]);
            
            $progr3 = DB::table('edu_programms')->insertGetId([
                'title' => 'Vadības prasmes valsts pārvaldē',
                'sub_title' => "Demo apakšvirsraksts - iespējams nav tāds lauks nepieciešams..",
                'parent_id' => $progr2,
                'is_published' => 1,
            ]);
            
            $subj1 = DB::table('edu_subjects')->insertGetId([
                'title' => 'Efektīva komandas sadarbība',
                'subject_type_id' => 1,
                'avail_id' => 1,
                'programm_id' => $progr3,
                'subject_code' => 'A1',
                'project_code' => 'U',
                'is_published' => 1,
            ]);
            
            $subj2 = DB::table('edu_subjects')->insertGetId([
                'title' => 'Efektīva sanāksmju vadīšana',
                'subject_type_id' => 1,
                'avail_id' => 1,
                'programm_id' => $progr3,
                'subject_code' => 'A2',
                'project_code' => 'U',
                'is_published' => 1,
            ]);
            
            $subj3 = DB::table('edu_subjects')->insertGetId([
                'title' => 'Stratēģija – tās izstrāde un ieviešana',
                'subject_type_id' => 1,
                'avail_id' => 1,
                'programm_id' => $progr3,
                'subject_code' => 'A3',
                'project_code' => 'U',
                'is_published' => 1,
            ]);
            
            $subj4 = DB::table('edu_subjects_groups')->insertGetId([                
                'subject_id' => $subj3,
                'teacher_id' => $teach2,
                'seats_limit' => 20,
                'signup_due' => '2018-05-19',
                'is_published' => 1,
            ]);
            // ---- End programms and subjects
            
            $this->rolesLists();
        });
    }   
    
    private function rolesLists() {
        $this->addRoleListFull(trans('db_dx_users.list_title_edu'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_dx_users.list_title_org'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_dx_users.list_title_teacher'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_dx_users.list_title_serv'), self::ROLE_MAIN);
        $this->addRoleListFull(trans('db_dx_users.list_title_student'), self::ROLE_MAIN);        
        $this->addRoleListRead(trans('db_dx_users.list_title_all'), self::ROLE_MAIN);
        
        $this->addRoleListFull(trans('db_dx_users.list_title_student'), self::ROLE_ORG);
        $this->addRoleListRead(trans('db_dx_users.list_title_all'), self::ROLE_ORG);
        $this->addRoleListRead(trans('db_dx_users.list_title_all'), self::ROLE_TEACH);
        
    }
    
    private function addRoleListFull($list_title, $role_id) {
        DB::table('dx_roles_lists')->insert([
            'role_id' => $role_id,
            'list_id' => DB::table('dx_lists')->where('list_title', '=', $list_title)->first()->id,
            'is_edit_rights' => 1,
            'is_delete_rights' => 1,
            'is_new_rights' => 1,
            'is_import_rights' => 1,
            'is_view_rights' => 1
        ]);
    }
    
    private function addRoleListRead($list_title, $role_id) {
        DB::table('dx_roles_lists')->insert([
            'role_id' => $role_id,
            'list_id' => DB::table('dx_lists')->where('list_title', '=', $list_title)->first()->id
        ]);
    }
   
}

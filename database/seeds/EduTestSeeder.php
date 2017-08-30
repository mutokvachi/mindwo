<?php

use Illuminate\Database\Seeder;

/**
 * Prepares testing users for an organization
 */
class EduTestSeeder extends Seeder
{      
    const ORG_TITLE = "Aizsardzības ministrija";
    const ORG_NR = '7778882222';
    const GEN_COUNT = 4000;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {    
        DB::transaction(function () {
            
            $org = DB::table('edu_orgs')
                      ->where('title', '=', self::ORG_TITLE)
                      ->first();

            if (!$org) {
                $org_id = DB::table('edu_orgs')->insertGetId([
                    'title' => self::ORG_TITLE,
                    'org_type_id' => 1,
                    'reg_nr' => self::ORG_NR,
                    'address' => 'Raiņa bulvāris 4, Rīga, LV-1050',
                ]);
            }
            else {
                $org_id = $org->id;

                // Clear old data
                $ousers = DB::table('edu_orgs_users')->where('org_id', '=', $org_id)->get();

                foreach($ousers as $ous) {
                    DB::table('edu_orgs_users')->where('id', '=', $ous->id)->delete();
                    DB::table('dx_users')->where('id', '=', $ous->user_id)->delete();
                }
            }
            
            $max_id = DB::table('dx_users')->max('id');

            for ($i=1; $i<self::GEN_COUNT; $i++) {
                // Create user

                $name_row = DB::table('in_saints_days')->where('id', '>', rand(0, 300))->where('txt', '!=', '')->first();
                $names = explode(",", $name_row->txt);
                
                $name = $names[rand(0, count($names)-1)];

                $fname_row = DB::table('in_saints_days')->where('id', '>', rand(301, 360))->where('txt', '!=', '')->first();
                $fnames = explode(",", $fname_row->txt);
                
                $fname = $fnames[rand(0, count($fnames)-1)];

                $user_id = DB::table('dx_users')->insertGetId([
                    'first_name' => $name,
                    'last_name' => $fname,
                    'person_code' => $max_id + $i,
                    'is_role_student' => 1,
                    'password' => '$2y$10$S803OIF2qhP8ZliilBZI9eYGCMXhhvAkiWuXXcG3J.Pp2hnImjv2q',
                    'login_name' => 'janis.supe@gmail.com',
                    'email' => 'janis.supe@gmail.com',
                    'phone' => '(+371) 29131987',
                    'mobile' => '(+371) 29131987'
                ]);
            
                DB::table('edu_orgs_users')->insert([
                    'user_id' => $user_id,
                    'org_id' => $org_id,
                    'job_title' => 'Klientu konsultants',
                    'email' => 'janis.supe@gmail.com',
                    'phone' => '(+371) 29131987',
                    'mobile' => '(+371) 29131987'
                ]);
            }
            
        });
    }  
   
}

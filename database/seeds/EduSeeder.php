<?php

use Illuminate\Database\Seeder;

class EduSeeder extends Seeder
{  
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {    
        DB::transaction(function () {
            
            DB::table('dx_users')->where('is_role_teacher', '=', 1)->delete();
            
            $teach1 = DB::table('dx_users')->insertGetId([
                'first_name' => 'Jānis',
                'last_name' => 'Kalniņš',
                'person_code' => rand('100000', '999999'),
                'is_role_teacher' => 1
            ]);
            
            $teach2 = DB::table('dx_users')->insertGetId([
                'first_name' => 'Aija',
                'last_name' => 'Bērziņa',
                'person_code' => rand('100000', '999999'),
                'is_role_teacher' => 1
            ]);
            
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
            
            $subj3 = DB::table('edu_subjects_groups')->insertGetId([                
                'subject_id' => $subj3,
                'teacher_id' => $teach2,
                'seats_limit' => 20,
                'signup_due' => '2018-05-19',
                'is_published' => 1,
            ]);
            
        });
    }   
   
}

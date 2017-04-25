<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersEmergencyTypesAddRows extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_users_emergency_types')->insert([
            'id' => 1,
            'title' => trans('db_dx_users_emergency_types.data_father')
        ]);
        
        DB::table('dx_users_emergency_types')->insert([
            'id' => 2,
            'title' => trans('db_dx_users_emergency_types.data_mother')
        ]);
        
        DB::table('dx_users_emergency_types')->insert([
            'id' => 3,
            'title' => trans('db_dx_users_emergency_types.data_brother')
        ]);
        
        DB::table('dx_users_emergency_types')->insert([
            'id' => 4,
            'title' => trans('db_dx_users_emergency_types.data_sister')
        ]);
        
        DB::table('dx_users_emergency_types')->insert([
            'id' => 5,
            'title' => trans('db_dx_users_emergency_types.data_wife')
        ]);
        
        DB::table('dx_users_emergency_types')->insert([
            'id' => 6,
            'title' => trans('db_dx_users_emergency_types.data_husband')
        ]);
        
        DB::table('dx_users_emergency_types')->insert([
            'id' => 7,
            'title' => trans('db_dx_users_emergency_types.data_friend')
        ]);
        
        DB::table('dx_users_emergency_types')->insert([
            'id' => 8,
            'title' => trans('db_dx_users_emergency_types.data_son')
        ]);
        
        DB::table('dx_users_emergency_types')->insert([
            'id' => 9,
            'title' => trans('db_dx_users_emergency_types.data_daughter')
        ]);
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_users_emergency_types')->delete();
    }
}

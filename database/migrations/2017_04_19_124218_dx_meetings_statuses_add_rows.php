<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxMeetingsStatusesAddRows extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_meetings_statuses')->insert([
            'id' => 1,
            'title' => trans('db_dx_meetings_statuses.data_future'),
            'code' => 'Future'
        ]);
        
        DB::table('dx_meetings_statuses')->insert([
            'id' => 2,
            'title' => trans('db_dx_meetings_statuses.data_active'),
            'code' => 'Active'
        ]);
        
        DB::table('dx_meetings_statuses')->insert([
            'id' => 3,
            'title' => trans('db_dx_meetings_statuses.data_past'),
            'code' => 'Past'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_meetings_statuses')->delete();
    }
}

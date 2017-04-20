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
            'title' => 'Sagatavošana',
            'code' => 'Future'
        ]);
        
        DB::table('dx_meetings_statuses')->insert([
            'id' => 2,
            'title' => 'Aktīvā',
            'code' => 'Active'
        ]);
        
        DB::table('dx_meetings_statuses')->insert([
            'id' => 3,
            'title' => 'Beigusies',
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

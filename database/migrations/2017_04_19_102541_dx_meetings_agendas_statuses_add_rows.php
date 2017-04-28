<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxMeetingsAgendasStatusesAddRows extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_meetings_agendas_statuses')->insert([
            'id' => 1,
            'title' => trans('db_dx_meetings_agendas_statuses.data_pending'),
            'code' => 'PENDING'
        ]);
        
        DB::table('dx_meetings_agendas_statuses')->insert([
            'id' => 2,
            'title' => trans('db_dx_meetings_agendas_statuses.data_in_process'),
            'code' => 'IN_PROCESS'
        ]);
        
        DB::table('dx_meetings_agendas_statuses')->insert([
            'id' => 3,
            'title' => trans('db_dx_meetings_agendas_statuses.data_processed'),
            'code' => 'PROCESSED'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_meetings_agendas_statuses')->delete();
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxMeetingsAgendasAddStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_meetings_agendas', function (Blueprint $table) {
            $table->integer('status_id')->unsigned()->nullable()->comment = trans('db_dx_tasks_statuses.is_revocable');
            
            $table->index('status_id');            
            $table->foreign('status_id')->references('id')->on('dx_meetings_agendas_statuses')->delete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_meetings_agendas', function (Blueprint $table) {            
            $table->dropForeign(['status_id']);
            $table->dropColumn(['status_id']);
        });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxMeetingsAddStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_meetings', function (Blueprint $table) {
            $table->integer('status_id')->unsigned()->nullable()->comment = trans('db_dx_meetings.status_id');
            
            $table->index('status_id');            
            $table->foreign('status_id')->references('id')->on('dx_meetings_statuses')->delete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_meetings', function (Blueprint $table) {            
            $table->dropForeign(['status_id']);
            $table->dropColumn(['status_id']);
        });
    }
}

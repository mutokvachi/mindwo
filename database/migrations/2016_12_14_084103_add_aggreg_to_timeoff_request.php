<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAggregToTimeoffRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_timeoff_requests', function (Blueprint $table) {
            $table->string('request_details', 2000)->nullable()->comment = "Request details";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_timeoff_requests', function (Blueprint $table) {
            $table->dropColumn(['request_details']);
        });
    }
}

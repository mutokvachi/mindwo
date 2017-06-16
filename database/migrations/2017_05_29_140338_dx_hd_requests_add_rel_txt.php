<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxHdRequestsAddRelTxt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_hd_requests', function (Blueprint $table) {
            $table->string('job_place_kind', 200)->nullable()->default(null)->comment = 'Darba vietas veids';
            $table->string('mobile_kind', 200)->nullable()->default(null)->comment = 'Mobilā tālruņa pieteikuma veids';
            $table->string('mobilly_kind', 200)->nullable()->default(null)->comment = 'Mobilly pieteikuma veids';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_hd_requests', function (Blueprint $table) {
            $table->dropColumn(['job_place_kind']);
            $table->dropColumn(['mobile_kind']);
            $table->dropColumn(['mobilly_kind']);
        });
    }
}

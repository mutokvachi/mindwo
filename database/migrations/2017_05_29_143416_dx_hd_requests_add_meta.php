<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxHdRequestsAddMeta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_hd_requests', function (Blueprint $table) {
            $table->integer('subj_employee_id')->nullable()->default(null)->comment = 'Darbinieks';
            $table->string('phone_nr', 50)->nullable()->default(null)->comment = 'Tālruņa nr.';
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
            $table->dropColumn(['subj_employee_id']);
            $table->dropColumn(['phone_nr']);
        });
    }
}

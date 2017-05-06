<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersAccrualPoliciesAddAlgorithmCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_users_accrual_policies', function (Blueprint $table) {
            $table->string('algorithm_code', 100)->nullable()->comment = trans('db_dx_users_accrual_policies.algorithm_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_users_accrual_policies', function (Blueprint $table) {
            $table->dropColumn(['algorithm_code']);
        });
    }
}

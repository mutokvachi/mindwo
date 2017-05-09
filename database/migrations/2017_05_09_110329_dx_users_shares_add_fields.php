<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersSharesAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_users_shares', function (Blueprint $table) {
            $table->string('ammount', 1000)->nullable()->comment = trans('db_dx_users_shares.ammount');
            $table->string('vesting', 1000)->nullable()->comment = trans('db_dx_users_shares.vesting');
            $table->string('cliff', 1000)->nullable()->comment = trans('db_dx_users_shares.cliff');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_users_shares', function (Blueprint $table) {
            $table->dropColumn(['ammount']);
            $table->dropColumn(['vesting']);
            $table->dropColumn(['cliff']);
        });
    }
}

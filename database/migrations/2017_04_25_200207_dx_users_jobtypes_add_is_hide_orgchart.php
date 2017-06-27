<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersJobtypesAddIsHideOrgchart extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_users_jobtypes', function (Blueprint $table) {
            $table->boolean('is_hide_orgchart')->nullable()->default(false)->comment = trans('db_dx_users.is_hide_orgchart');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_users_jobtypes', function (Blueprint $table) {
            $table->dropColumn(['is_hide_orgchart']);
        });
    }
}

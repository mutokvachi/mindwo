<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersAddRolesFields extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_users', function (Blueprint $table) {
            $table->boolean('is_role_coordin_main')->nullable()->default(false)->comment = trans('dx_users.is_role_coordin_main');
            $table->boolean('is_role_coordin')->nullable()->default(false)->comment = trans('dx_users.is_role_coordin');
            $table->boolean('is_role_teacher')->nullable()->default(false)->comment = trans('dx_users.is_role_teacher');
            $table->boolean('is_role_student')->nullable()->default(false)->comment = trans('dx_users.is_role_student');
            $table->boolean('is_role_supply')->nullable()->default(false)->comment = trans('dx_users.is_role_supply');
            $table->boolean('is_anonim')->nullable()->default(false)->comment = trans('dx_users.is_anonim');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_users', function (Blueprint $table) {
            $table->dropColumn(['is_role_coordin_main']);
            $table->dropColumn(['is_role_coordin']);
            $table->dropColumn(['is_role_teacher']);
            $table->dropColumn(['is_role_student']);
            $table->dropColumn(['is_role_supply']);
            $table->dropColumn(['is_anonim']);
        });
    }
}

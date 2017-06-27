<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxRolesListsAddRights extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_roles_lists', function (Blueprint $table) {
            $table->boolean('is_import_rights')->nullable()->default(false)->comment = trans('db_dx_roles_lists.is_import_rights');
            $table->boolean('is_view_rights')->nullable()->default(false)->comment = trans('db_dx_roles_lists.is_view_rights');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_roles_lists', function (Blueprint $table) {
            $table->dropColumn(['is_import_rights']);
            $table->dropColumn(['is_view_rights']);
        });
    }
}

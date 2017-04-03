<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxRolesListsAddIsSubord extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_roles_lists', function (Blueprint $table) {
            $table->boolean('is_subord')->default(false)->comment = trans('db_dx_roles_lists.is_subord');
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
            $table->dropColumn(['is_subord']);
        });
    }
}

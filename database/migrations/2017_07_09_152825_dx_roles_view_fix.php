<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxRolesViewFix extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_views')
                ->where('id', '=', 22)
                ->whereNotNull('custom_sql')
                ->update([
                    'custom_sql' => "select * from (select dx_roles.id as id, dx_roles.title as dx_roles_title, title, is_system, description, is_default FROM dx_roles WHERE id != 1 or [ME] in (SELECT user_id FROM dx_users_roles WHERE role_id = 1)) t WHERE 1=1"
                ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_views')
                ->where('id', '=', 22)
                ->whereNotNull('custom_sql')
                ->update([
                    'custom_sql' => "select * from (select dx_roles.id as id, dx_roles.title as dx_roles_title, title, is_system, description FROM dx_roles WHERE id != 1 or [ME] in (SELECT user_id FROM dx_users_roles WHERE role_id = 1)) t WHERE 1=1"
                ]);
    }
}

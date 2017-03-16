<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxMenuAddRoleIdUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
           
            $list_id = App\Libraries\DBHelper::getListByTable('dx_menu')->id;
            $role_list_id = App\Libraries\DBHelper::getListByTable('dx_roles')->id;
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'role_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_RELATED,
                'title_list' => 'Role',
                'title_form' => 'Role',
                'rel_list_id' => $role_list_id,
                'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $role_list_id)->where('db_name', '=', 'title')->first()->id,
            ]);
        
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, []);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::transaction(function () {
            $list_id = App\Libraries\DBHelper::getListByTable('dx_menu')->id;
            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'role_id');

        });
    }
}

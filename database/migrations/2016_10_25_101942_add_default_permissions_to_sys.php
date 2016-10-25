<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultPermissionsToSys extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_roles');

        if (!$list) {
            return;
        }

        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list->id,
            'db_name' => 'is_default',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
            'title_list' => 'Default role for new users',
            'title_form' => 'Default role for new users',
        ]);

        App\Libraries\DBHelper::addFieldToForm($list->id, $fld_id);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_roles');
        
        if ($list) {
            App\Libraries\DBHelper::dropField($list->id, 'is_default');
        } 
    }
}

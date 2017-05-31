<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxCryptoRegenUiCreatedUserFix extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $list_id = App\Libraries\DBHelper::getListByTable('dx_crypto_regen')->id;
        
        // ADD RELATION FOR CREATED USER FIELD
        DB::table('dx_lists_fields')
                ->where('list_id', '=', $list_id)
                ->where('db_name', '=', 'created_user_id')
                ->update([
                    'rel_list_id' => Config::get('dx.employee_list_id'),
                    'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', Config::get('dx.employee_list_id'))->where('db_name', '=', 'display_name')->first()->id
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

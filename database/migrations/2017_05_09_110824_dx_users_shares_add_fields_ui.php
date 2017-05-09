<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersSharesAddFieldsUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_users_shares');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){
                        
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'ammount',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => trans('db_dx_users_shares.ammount'),
                'title_form' => trans('db_dx_users_shares.ammount'),
                'max_lenght' => 1000,
                'is_crypted' => 1
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 42, 'row_type_id' => 2]);            
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'vesting',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => trans('db_dx_users_shares.vesting'),
                'title_form' => trans('db_dx_users_shares.vesting'),
                'max_lenght' => 1000,
                'is_crypted' => 1
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id,['order_index' => 44, 'row_type_id' => 2]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'cliff',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => trans('db_dx_users_shares.cliff'),
                'title_form' => trans('db_dx_users_shares.cliff'),
                'max_lenght' => 1000,
                'is_crypted' => 1
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id,['order_index' => 46, 'row_type_id' => 2]);
            
            $fld = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'shares')->first();
            
            DB::table('dx_forms_fields')->where('field_id', '=', $fld->id)->update(['row_type_id' => 2]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_users_shares');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'ammount');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'vesting');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'cliff');
            
            $fld = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'shares')->first();
            
            DB::table('dx_forms_fields')->where('field_id', '=', $fld->id)->update(['row_type_id' => 1]);
        });
    }
}

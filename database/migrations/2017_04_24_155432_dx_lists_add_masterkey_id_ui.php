<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxListsAddMasterkeyIdUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_lists');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){
                     
            $group_list_id = App\Libraries\DBHelper::getListByTable('dx_crypto_masterkey_groups')->id;
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'masterkey_group_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_RELATED,
                'title_list' => trans('crypto.db.master_key_group_title'),
                'title_form' => trans('crypto.db.master_key_group_title'),
                'rel_list_id' => $group_list_id,
                'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $group_list_id)->where('db_name', '=', 'title')->first()->id,
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
         $list = App\Libraries\DBHelper::getListByTable('dx_lists');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'masterkey_group_id');            
        });
    }
}

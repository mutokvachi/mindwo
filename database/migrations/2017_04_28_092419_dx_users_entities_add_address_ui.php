<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersEntitiesAddAddressUi extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_users_entities');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){
                        
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'address',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => 'Address',
                'title_form' => 'Address',
                'max_lenght' => 1000
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'address_styled',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => 'Styled address',
                'title_form' => 'Styled address',
                'max_lenght' => 1000,
                'hint' => 'Here can be provided address with styled look, for example, by using seperators |. This address can be used for Word documents generation.'
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_users_entities');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'address');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'address_styled');
        });
    }
}

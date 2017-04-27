<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxListsFieldsAddIsCryptedUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_lists_fields');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){
                    
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'is_crypted',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => trans('db_dx_lists_fields.is_crypted'),
                'title_form' => trans('db_dx_lists_fields.is_crypted'),
                'hint' => trans('db_dx_lists_fields.hint_is_crypted')
            ]);
        
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, []);
            
            \App\Libraries\DBHelper::removeJavaScriptFromForm('dx_lists_fields', 'Parāda vai paslēpj laukus atkarībā no izvēlētā tipa');
            
            \App\Libraries\DBHelper::addJavaScriptToForm('dx_lists_fields', '2017_04_20_dx_lists_fields_crypto.js', 'Parāda vai paslēpj laukus atkarībā no izvēlētā tipa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_lists_fields');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'is_crypted');
        });
    }
}

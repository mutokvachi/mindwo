<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxListsFieldsAddItemsUi extends Migration
{    /**
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
                'db_name' => 'items',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_LONG_TEXT,
                'title_list' => trans('db_dx_lists_fields.items'),
                'title_form' => trans('db_dx_lists_fields.items'),
                'max_lenght' => 4000,
                'hint' => trans('db_dx_lists_fields.hint_items')
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 85]);    
            
            \App\Libraries\DBHelper::removeJavaScriptFromForm('dx_lists_fields', trans('db_dx_lists_fields.js_showhide'));
            \App\Libraries\DBHelper::addJavaScriptToForm('dx_lists_fields', '2017_05_29_dx_lists_fields.js', trans('db_dx_lists_fields.js_showhide'));

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
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'items');
        });
    }
}

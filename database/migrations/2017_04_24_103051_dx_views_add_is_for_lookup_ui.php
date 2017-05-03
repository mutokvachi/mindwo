<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxViewsAddIsForLookupUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_views');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){
                        
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'is_for_lookup',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => trans('db_dx_views.is_for_lookup'),
                'title_form' => trans('db_dx_views.is_for_lookup'),
                'hint' => trans('db_dx_views.hint_is_for_lookup')
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
        $list = App\Libraries\DBHelper::getListByTable('dx_views');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'is_for_lookup');            
        });
    }
}
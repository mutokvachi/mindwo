<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxListsFieldsJs extends Migration
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
            
            \App\Libraries\DBHelper::addJavaScriptToForm($list_id, '2017_06_07_dx_lists_fields.js', trans('db_dx_lists_fields.js_db_name'));

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
            \App\Libraries\DBHelper::removeJavaScriptFromForm($list_id, trans('db_dx_lists_fields.js_db_name'));
        });
    }
}

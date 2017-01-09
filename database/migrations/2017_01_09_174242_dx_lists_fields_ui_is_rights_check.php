<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxListsFieldsUiIsRightsCheck extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            $list = App\Libraries\DBHelper::getListByTable("dx_lists_fields");            
                        
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list->id,
                'db_name' => 'is_right_check',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => trans('db_dx_lists_fields.is_right_check_list'),
                'title_form' => trans('db_dx_lists_fields.is_right_check_form'),
                'hint' => trans('db_dx_lists_fields.is_right_check_hint'),
            ]);
        
            App\Libraries\DBHelper::addFieldToForm($list->id, $fld_id, ['order_index' => 210, 'row_type_id' => 2]);
            
            $syn_id = DB::table('dx_lists_fields')->where('db_name', '=', 'is_fields_synchro')->where('list_id', '=', $list->id)->first()->id;
            DB::table('dx_forms_fields')->where('field_id', '=', $syn_id)->update(['row_type_id' => 2]);
            
            \App\Libraries\DBHelper::removeJavaScriptFromForm('dx_lists_fields', trans('db_dx_lists_fields.js_showhide'));
            \App\Libraries\DBHelper::addJavaScriptToForm('dx_lists_fields', '2017_01_09_dx_lists_fields.js', trans('db_dx_lists_fields.js_showhide'));
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
            App\Libraries\DBHelper::removeFieldCMS("dx_lists_fields", "is_right_check");
        });
    }
}

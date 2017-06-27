<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxWorkflowsDefAddIsAnyDelegateUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        
        $list = App\Libraries\DBHelper::getListByTable('dx_workflows_def');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        $form_id = DB::table('dx_forms')->where('list_id', '=', $list_id)->first()->id;
        
        DB::transaction(function () use ($list_id, $form_id){
                 
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'is_any_delegate',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => trans('db_dx_workflows_def.is_any_delegate'),
                'title_form' => trans('db_dx_workflows_def.is_any_delegate'),
                'hint' => trans('db_dx_workflows_def.hint_is_any_delegate')
            ]);
            
            DB::table('dx_forms_fields')
                    ->where('form_id', '=', $form_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'is_custom_approve')->first()->id)
                    ->update(['row_type_id' => 2]);
                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 48, 'row_type_id' => 2]);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_workflows_def');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'is_any_delegate');            
        });
    }
}

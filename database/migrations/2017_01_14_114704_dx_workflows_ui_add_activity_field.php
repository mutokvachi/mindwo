<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxWorkflowsUiAddActivityField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            
            $list_id = App\Libraries\DBHelper::getListByTable('dx_workflows')->id;
            $rel_list_id = App\Libraries\DBHelper::getListByTable('dx_workflows_activities')->id;
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'activity_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_RELATED,
                'rel_list_id' => $rel_list_id,
                'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $rel_list_id)->where('db_name', '=', 'title')->first()->id,
                'title_list' => trans('workflow.fld_activity'),
                'title_form' => trans('workflow.fld_activity')
            ]);
        
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index'=>510]);
            
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
            $list_id = App\Libraries\DBHelper::getListByTable('dx_workflows')->id;
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'activity_id');
        });
    }
}

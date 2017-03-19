<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxTasksStatusesAddIsRevocableUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_tasks_statuses');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){
                    
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'is_revocable',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => trans('db_dx_tasks_statuses.is_revocable'),
                'title_form' => trans('db_dx_tasks_statuses.is_revocable')
            ]);
        
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, []);
            
            DB::table('dx_tasks_statuses')->where('id', '=', 1)->update(['is_revocable' => true]);
            DB::table('dx_tasks_statuses')->where('id', '=', 2)->update(['is_revocable' => false]);
            DB::table('dx_tasks_statuses')->where('id', '=', 3)->update(['is_revocable' => false]);
            DB::table('dx_tasks_statuses')->where('id', '=', 4)->update(['is_revocable' => false]);
            DB::table('dx_tasks_statuses')->where('id', '=', 5)->update(['is_revocable' => true]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_tasks_statuses');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'is_revocable');
        });
    }
}

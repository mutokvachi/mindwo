<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxTasksStatusesAddColorClassUi extends Migration
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
                'db_name' => 'color',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_COLOR,
                'title_list' => trans('db_dx_tasks_statuses.color'),
                'title_form' => trans('db_dx_tasks_statuses.color'),
                'max_lenght' => 50
            ]);
        
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, []);
            
            DB::table('dx_tasks_statuses')->where('id', '=', 1)->update(['color' => 'rgba(142,155,174,1)']);
            DB::table('dx_tasks_statuses')->where('id', '=', 2)->update(['color' => 'rgba(54,198,211,1)']);
            DB::table('dx_tasks_statuses')->where('id', '=', 3)->update(['color' => 'rgba(237,107,117,1)']);
            DB::table('dx_tasks_statuses')->where('id', '=', 4)->update(['color' => 'rgba(47,53,59,1)']);
            DB::table('dx_tasks_statuses')->where('id', '=', 5)->update(['color' => 'rgba(241,196,15,1)']);
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
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'color');
        });
    }
}

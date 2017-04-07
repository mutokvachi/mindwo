<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxViewsAddGroupIdUi extends Migration
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
                     
            $group_list_id = App\Libraries\DBHelper::getListByTable('dx_views_reports_groups')->id;
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'group_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_RELATED,
                'title_list' => trans('db_dx_views.group'),
                'title_form' => trans('db_dx_views.group'),
                'rel_list_id' => $group_list_id,
                'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $group_list_id)->where('db_name', '=', 'title')->first()->id,
            ]);            
            $form_id = DB::table('dx_forms')->where('list_id', '=', $list_id)->first()->id; 
            $tab_id = DB::table('dx_forms_tabs')->where('form_id', '=', $form_id)->where('title', '=', trans('db_dx_views.tab_report'))->first()->id; 
                           
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['tab_id' => $tab_id, 'row_type_id' => 2]);
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
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'group_id');            
        });
    }
}

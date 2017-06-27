<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxViewsAddReportsUi extends Migration
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
            $form_id = DB::table('dx_forms')->where('list_id', '=', $list_id)->first()->id;            
            $tab_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form_id, 
                'title' => trans('db_dx_views.tab_report'), 
                'order_index' => 50,
                'is_custom_data' => 1
            ]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'is_report',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => trans('db_dx_views.is_report'),
                'title_form' => trans('db_dx_views.is_report'),
                'hint' => trans('db_dx_views.hint_is_report')
            ]);            
            
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['tab_id' => $tab_id, 'row_type_id' => 2]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'is_builtin',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => trans('db_dx_views.is_builtin'),
                'title_form' => trans('db_dx_views.is_builtin'),
                'hint' => trans('db_dx_views.hint_is_builtin')
            ]);            
            
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['tab_id' => $tab_id, 'row_type_id' => 2]);
            
            $field_list_id = App\Libraries\DBHelper::getListByTable('dx_lists_fields')->id;
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'filter_field_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_RELATED,
                'title_list' => trans('db_dx_views.filter_field_id'),
                'title_form' => trans('db_dx_views.filter_field_id'),
                'hint' => trans('db_dx_views.hint_filter_field_id'),
                'rel_list_id' => $field_list_id,
                'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $field_list_id)->where('db_name', '=', 'title_list')->first()->id,
            ]);            
            
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
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'is_report');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'is_builtin');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'filter_field_id');
            
            $form_id = DB::table('dx_forms')->where('list_id', '=', $list_id)->first()->id;
            
            DB::table('dx_forms_tabs')->where('form_id', '=', $form_id)->where('title', '=', trans('db_dx_views.tab_report'))->delete();          
            
        });
    }
}

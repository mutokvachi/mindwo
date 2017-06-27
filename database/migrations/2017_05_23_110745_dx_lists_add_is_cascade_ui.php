<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxListsAddIsCascadeUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        
        $list = App\Libraries\DBHelper::getListByTable('dx_lists');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        $form_id = DB::table('dx_forms')->where('list_id', '=', $list_id)->first()->id;
        $max_tab_index = DB::table('dx_forms_tabs')->where('form_id', '=', $form_id)->max('order_index') + 10;
        
        DB::transaction(function () use ($list_id, $form_id, $max_tab_index){
            
            $tab_id = DB::table('dx_forms_tabs')->insertGetId(['form_id' => $form_id, 'title' => trans('db_dx_lists.tab_settings'), 'is_custom_data' => 1, 'order_index' => $max_tab_index]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'is_cascade_delete',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => trans('db_dx_lists.is_cascade_delete'),
                'title_form' => trans('db_dx_lists.is_cascade_delete'),
                'hint' => trans('db_dx_lists.hint_is_cascade_delete')
            ]);
            
            DB::table('dx_forms_fields')
                    ->where('form_id', '=', $form_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'id')->first()->id)
                    ->update(['row_type_id' => 1]);            
            
            DB::table('dx_forms_fields')
                    ->where('form_id', '=', $form_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'object_id')->first()->id)
                    ->update(['row_type_id' => 2]);
            
            DB::table('dx_forms_fields')
                    ->where('form_id', '=', $form_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'list_title')->first()->id)
                    ->update(['row_type_id' => 2]);
            
            DB::table('dx_forms_fields')
                    ->where('form_id', '=', $form_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'template_name')->first()->id)
                    ->update(['tab_id' => $tab_id, 'order_index' => 200]);
            
            DB::table('dx_forms_fields')
                    ->where('form_id', '=', $form_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'group_id')->first()->id)
                    ->update(['tab_id' => $tab_id, 'order_index' => 230, 'group_label' => trans('db_dx_lists.section_documentation')]);
            
            DB::table('dx_forms_fields')
                    ->where('form_id', '=', $form_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'hint')->first()->id)
                    ->update(['tab_id' => $tab_id, 'order_index' => 240]);
            
            DB::table('dx_forms_fields')
                    ->where('form_id', '=', $form_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'masterkey_group_id')->first()->id)
                    ->update(['tab_id' => $tab_id, 'order_index' => 210]);
                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['tab_id' => $tab_id, 'order_index' => 220]);
            
            
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_lists');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        $form_id = DB::table('dx_forms')->where('list_id', '=', $list_id)->first()->id;
        $tab_id = DB::table('dx_forms_tabs')->where('form_id', '=', $form_id)->where('title', '=', trans('db_dx_lists.tab_settings'))->first()->id;
        
        DB::transaction(function () use ($list_id, $tab_id, $form_id){            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'is_cascade_delete');
            DB::table('dx_forms_fields')
                    ->where('form_id', '=', $form_id)
                    ->where('tab_id', '=', $tab_id)
                    ->update(['tab_id' => null]);
            
            DB::table('dx_forms_tabs')->where('form_id', '=', $form_id)->where('title', '=', trans('db_dx_lists.tab_settings'))->delete();
        });
    }
}

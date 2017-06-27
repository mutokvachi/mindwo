<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxRolesListsAddRightsUi extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        
        $list = App\Libraries\DBHelper::getListByTable('dx_roles_lists');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){
            
            $form_fields = DB::table('dx_forms_fields')->where('form_id', '=', 21)->orderBy('order_index')->get();
            foreach($form_fields as $key => $ffld) {
                DB::table('dx_forms_fields')->where('id', '=', $ffld->id)->update(['order_index' => $key*10]);
            }
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'is_import_rights',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => trans('db_dx_roles_lists.is_import_rights'),
                'title_form' => trans('db_dx_roles_lists.is_import_rights')
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 55]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'is_view_rights',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => trans('db_dx_roles_lists.is_view_rights'),
                'title_form' => trans('db_dx_roles_lists.is_view_rights')
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 57]);
            
            $form_fields = DB::table('dx_forms_fields')->where('form_id', '=', 21)->orderBy('order_index')->get();
            foreach($form_fields as $key => $ffld) {
                DB::table('dx_forms_fields')->where('id', '=', $ffld->id)->update(['order_index' => $key*10]);
            }
            
            DB::table('dx_roles_lists')->where('role_id', '=', 1)->update(['is_view_rights' => 1, 'is_import_rights' => 1]);
            
            $hr_role = DB::table('dx_roles')->where('title', '=', 'HR')->first();
            if ($hr_role) {
                DB::table('dx_roles_lists')->where('role_id', '=', $hr_role->id)->update(['is_view_rights' => 1, 'is_import_rights' => 1]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_roles_lists');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'is_import_rights'); 
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'is_view_rights'); 
        });
    }
}

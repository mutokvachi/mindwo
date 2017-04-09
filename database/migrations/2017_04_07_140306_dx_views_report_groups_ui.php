<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxViewsReportGroupsUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        DB::transaction(function () {
            $table_name = "dx_views_reports_groups";
            $list_name = trans('db_dx_views_reports_groups.list_name');
            $item_name = trans('db_dx_views_reports_groups.item_name');

             // create register
            $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $table_name, 'title' => $list_name , 'is_history_logic' => 1]);
            $list_gen = new Structure\StructMethod_register_generate();
            $list_gen->obj_id = $obj_id;
            $list_gen->register_title = $list_name;
            $list_gen->form_title = $item_name;
            $list_gen->doMethod();

            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;       
            
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'icon')
                    ->update(['hint' => trans('db_dx_views_reports_groups.icon_hint')]);
                    
            // reorganize view fields - hide or remove unneeded
            \App\Libraries\DBHelper::removeFieldsFromAllViews($table_name, ['id'], true); // hide ID field                       
                    
            // user rights
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
            
            // menu
            $settings_menu_id = 63;
            
            DB::table('dx_menu')->insertGetId(['parent_id' => $settings_menu_id, 'title'=>$list_name, 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $settings_menu_id)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
                        
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
            \App\Libraries\DBHelper::deleteRegister('dx_views_reports_groups');
            DB::table('dx_objects')->where('db_name', '=', 'dx_views_reports_groups')->delete();
        });
    }
}

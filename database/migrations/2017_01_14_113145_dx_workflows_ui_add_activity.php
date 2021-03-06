<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxWorkflowsUiAddActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            $table_name = "dx_workflows_activities";
            $list_name = "Workflows activities";
            $item_name = "Workflow activity";

             // create register
            $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $table_name, 'title' => $list_name , 'is_history_logic' => 1]);
            $list_gen = new Structure\StructMethod_register_generate();
            $list_gen->obj_id = $obj_id;
            $list_gen->register_title = $list_name;
            $list_gen->form_title = $item_name;
            $list_gen->doMethod();

            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;       

            // reorganize view fields - hide or remove unneeded
            \App\Libraries\DBHelper::removeFieldsFromAllViews($table_name, ['id'], true); // hide ID field
            
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
            
            DB::table('dx_menu')->insertGetId(['parent_id' => 12, 'title'=>$list_name, 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', 12)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
            
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
            \App\Libraries\DBHelper::deleteRegister('dx_workflows_activities');
            DB::table('dx_objects')->where('db_name', '=', 'dx_workflows_activities')->delete();
        });
    }
}

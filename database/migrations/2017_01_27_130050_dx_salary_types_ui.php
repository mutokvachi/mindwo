<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxSalaryTypesUi extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        DB::transaction(function () {
            $table_name = "dx_salary_types";
            $list_name = "Salary types";
            $item_name = "Salary type";

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
                    
            // user rights
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
            DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR           
                       
            // make menu
            $parent = DB::table('dx_menu')->where('title', '=', 'Classifiers')->first();
            
            if ($parent) {
                DB::table('dx_menu')->insert(['parent_id' => $parent->id, 'title'=>$list_name, 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $parent->id)->max('order_index')+10), 'position_id' => 1, 'group_id' => 1]);
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
        DB::transaction(function () {
            \App\Libraries\DBHelper::deleteRegister('dx_salary_types');
            DB::table('dx_objects')->where('db_name', '=', 'dx_salary_types')->delete();
        });
    }
}

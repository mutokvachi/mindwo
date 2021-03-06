<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxFormsActionsUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
    {
        DB::transaction(function () {
            $table_name = "dx_forms_actions";
            $list_name = trans('db_dx_forms_actions.list_name');
            $item_name = trans('db_dx_forms_actions.item_name');

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
            
            // insert tab in form
            $form_list = \App\Libraries\DBHelper::getListByTable('dx_forms');
            $form = DB::table('dx_forms')->where('list_id', '=', $form_list->id)->first();
            DB::table('dx_forms_tabs')->insert([
                'form_id' => $form->id,                
                'title' => trans('db_dx_forms_actions.tab_title'),
                'grid_list_id' => $list_id,
                'grid_list_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'form_id')->first()->id,
                'order_index' => (DB::table('dx_forms_tabs')->where('form_id', '=', $form_list->id)->max('order_index')+10)
            ]);
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
            \App\Libraries\DBHelper::deleteRegister('dx_forms_actions');
            DB::table('dx_objects')->where('db_name', '=', 'dx_forms_actions')->delete();
        });
    }
}

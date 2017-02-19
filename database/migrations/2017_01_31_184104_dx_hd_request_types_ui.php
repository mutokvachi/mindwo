<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxHdRequestTypesUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            $table_name = "dx_hd_request_types";
            $list_name = "Pieteikumu veidi";
            $item_name = "Pieteikuma veids";

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
            
            // adjust form look & feel
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'full_path')
                                                ->first()->id)
                    ->update(['is_hidden' => 1]);
            
            DB::table('dx_lists_fields')
            ->where('list_id', '=', $list_id)
            ->where('db_name', '=', 'full_path')
            ->update([
                'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_LONG_TEXT,
                'max_lenght' => 4000
            ]);
                    
            // user rights
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
                        
            // make menu
            $parent = DB::table('dx_menu')->where('title', '=', 'Klasifikatori')->first();
            
            if ($parent) {
                $par_id = DB::table('dx_menu')->insertgetId(['parent_id' => $parent->id, 'title' => 'Atbalsts', 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $parent->id)->max('order_index')+10), 'position_id' => 1, 'group_id' => 1]);
                DB::table('dx_menu')->insertgetId(['parent_id' => $par_id, 'title'=>$list_name, 'list_id'=>$list_id, 'order_index' => 10]);
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
            \App\Libraries\DBHelper::deleteRegister('dx_hd_request_types');
            DB::table('dx_objects')->where('db_name', '=', 'dx_hd_request_types')->delete();
            DB::table('dx_menu')->where('title', '=', 'Atbalsts')->delete();
        });
    }
}

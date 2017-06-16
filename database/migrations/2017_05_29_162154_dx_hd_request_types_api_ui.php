<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxHdRequestTypesApiUi extends Migration
{
  /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!$this->isHelpDeskUI()) {
            return;
        }
        
        DB::transaction(function () {
            $table_name = "dx_hd_request_types";
            $list_name = "Pieteikumu veidi - biroja atbalsts API";
            $item_name = "Pieteikuma veids - biroja atbalsts API";

             // create register
            $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $table_name, 'title' => $list_name , 'is_history_logic' => 1]);
            $list_gen = new Structure\StructMethod_register_generate();
            $list_gen->obj_id = $obj_id;
            $list_gen->register_title = $list_name;
            $list_gen->form_title = $item_name;
            $list_gen->doMethod();

            // get list
            $list_id = DB::table('dx_lists')->where('object_id', '=', $obj_id)->where('list_title', '=', $list_name)->first()->id;            
            
            // rights
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 0, 'is_delete_rights' => 0, 'is_new_rights' => 0]); // Sys admins
            DB::table('dx_roles_lists')->insert(['role_id' => 35, 'list_id' => $list_id, 'is_edit_rights' => 0, 'is_delete_rights' => 0, 'is_new_rights' => 0]); // My tasks performers
               
            // update list_id
            $def_list = DB::table('dx_lists')->where('list_title', '=', 'Pieteikumu veidi - biroja atbalsts')->first();
           
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'multi_list_id')
                    ->update([
                        'default_value' => $def_list->id,
                        'operation_id' => 1,
                        'criteria' => $def_list->id
                    ]);
            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'resp_admin_id');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'resp_junior_id');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'resp_programmer_id');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'full_path');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'order_index');
            
        });
    }    
  
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!$this->isHelpDeskUI()) {
            return;
        }
        
        DB::transaction(function () {
            
            $list = DB::table('dx_lists as l')
                    ->where('list_title', '=', 'Pieteikumu veidi - biroja atbalsts API')
                    ->first();                        
                        
            \App\Libraries\DBHelper::deleteRegister($list->id);
            
            DB::table('dx_objects')->where('title','=', 'Pieteikumu veidi - biroja atbalsts API')->delete();
            
        });
    }
    
    private function isHelpDeskUI() {
        $klasif_menu = DB::table('dx_menu')->where('title', '=', 'Biroja atbalsts')->first();
          
        return ($klasif_menu) ? true : false;
    }
}

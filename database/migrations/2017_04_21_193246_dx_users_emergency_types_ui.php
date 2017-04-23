<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;
use Illuminate\Support\Facades\Config;

class DxUsersEmergencyTypesUi extends Migration
{

    private $table_name = "dx_users_emergency_types";

    private $is_hr_ui = false;
    private $hr_role_id = 0;
    
    private function checkUI_Role() {
        $list_id = Config::get('dx.employee_list_id', 0);
        
        $this->is_hr_ui = ($list_id > 0);   
        
        $row_role  = DB::table('dx_roles')->where('title', '=', 'HR')->first();
        
        if ($row_role) {
            $this->hr_role_id = $row_role->id;
        }
    }
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->checkUI_Role();
        
        if ($this->hr_role_id == 0 || !$this->is_hr_ui) {
            return;
        }
        
        DB::transaction(function ()
        {
            $list_name = trans('db_'. $this->table_name . '.list_name');
            $item_name = trans('db_'. $this->table_name . '.item_name');

            // create register
            $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $this->table_name, 'title' => $list_name, 'is_history_logic' => 0]);
            $list_gen = new Structure\StructMethod_register_generate();
            $list_gen->obj_id = $obj_id;
            $list_gen->register_title = $list_name;
            $list_gen->form_title = $item_name;
            $list_gen->doMethod();

            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id;

            // reorganize view fields - hide or remove unneeded
            \App\Libraries\DBHelper::removeFieldsFromAllViews($this->table_name, ['id'], true); // hide ID field                       
            
            // user rights
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
            DB::table('dx_roles_lists')->insert(['role_id' => $this->hr_role_id, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
           
            // menu
            $parent_menu = DB::table('dx_menu')->where('title', '=', trans('db_'. $this->table_name . '.parent_menu'))->first();
            
            if ($parent_menu) {
                DB::table('dx_menu')->insertGetId(['parent_id' => $parent_menu->id, 'title' => $list_name, 'list_id' => $list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $parent_menu->id)->max('order_index') + 10), 'group_id' => 1, 'position_id' => 1]);
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
        $this->checkUI_Role();
        
        if ($this->hr_role_id == 0 || !$this->is_hr_ui) {
            return;
        }
        
        DB::transaction(function ()
        {
            \App\Libraries\DBHelper::deleteRegister($this->table_name);
            DB::table('dx_objects')->where('db_name', '=', $this->table_name)->delete();
        });
    }

}

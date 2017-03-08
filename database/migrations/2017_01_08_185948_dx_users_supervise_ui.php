<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxUsersSuperviseUi extends Migration
{
    private $is_hr_ui = false;
    private $is_hr_role = false;
    private $hr_role_id = 0;
    private $public_role_id = 0;
    
    private function checkUI_Role() {
        $list_id = Config::get('dx.employee_list_id', 0);
        
        $this->is_hr_ui = ($list_id > 0);   
        
        $this->is_hr_role  = (App::getLocale() == 'en');
        
        $this->hr_role_id = App\Libraries\DBHelper::getOrCreateRoleID('HR');
        $this->public_role_id = App\Libraries\DBHelper::getOrCreateRoleID('HR guest');
    }
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->checkUI_Role();
        
        DB::transaction(function () {
            $table_name = "dx_users_supervise";
            $list_name = "Users supervision domains";
            $item_name = "Users supervision domain";

             // create register
            $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $table_name, 'title' => $list_name , 'is_history_logic' => 1]);
            $list_gen = new Structure\StructMethod_register_generate();
            $list_gen->obj_id = $obj_id;
            $list_gen->register_title = $list_name;
            $list_gen->form_title = $item_name;
            $list_gen->doMethod();

            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;       

            //fix user field (because we have 2 registers in 1 table dx_users)
            if ($this->is_hr_ui) {
                DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'user_id')
                    ->update([
                        'rel_list_id'=>Config::get('dx.employee_list_id'),
                        'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', Config::get('dx.employee_list_id'))->where('db_name', '=', 'display_name')->first()->id,
                        'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_LOOKUP
                    ]);
            }
            
            // reorganize view fields - hide or remove unneeded
            \App\Libraries\DBHelper::removeFieldsFromAllViews($table_name, ['id'], true); // hide ID field
            
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
            
            $rights_menu = DB::table('dx_menu')->where('title', '=', 'User rights')->first();
            if ($rights_menu) {
                DB::table('dx_menu')->insertGetId(['parent_id' => $rights_menu->id, 'title'=>$list_name, 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $rights_menu->id)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
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
            \App\Libraries\DBHelper::deleteRegister('dx_users_supervise');
            DB::table('dx_objects')->where('db_name', '=', 'dx_users_supervise')->delete();
        });
    }
}

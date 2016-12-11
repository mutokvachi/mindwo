<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

use Illuminate\Support\Facades\Config;

class TimeoffRequestUi extends Migration
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
            $table_name = "dx_timeoff_requests";
            $list_name = "Time off requests";
            $item_name = "Time off request";

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

            // set rights
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
            DB::table('dx_roles_lists')->insert(['role_id' => $this->hr_role_id, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR
            $fld_id = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'user_id')->first()->id;
            DB::table('dx_roles_lists')->insert(['role_id' => $this->public_role_id, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1, 'user_field_id' => $fld_id]); //HR guest

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

            // adjust fields in form
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name','=', 'from_date')
                                                ->first()->id)
                    ->update(['row_type_id' => 2]);

            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name','=', 'to_date')
                                                ->first()->id)
                    ->update(['row_type_id' => 2]);
            
            // set read only fields
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name','=', 'user_id')
                                                ->first()->id)
                    ->update(['is_readonly' => 1]);
            
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name','=', 'dx_item_status_id')
                                                ->first()->id)
                    ->update(['is_readonly' => 1]);

            // set default value [ME]
            DB::table('dx_lists_fields')
            ->where('list_id', '=', $list_id)
            ->where('db_name','=', 'user_id')
            ->update(['default_value'=>'[ME]']);
                    
            if ($this->is_hr_ui) {
                // menu
                $rep_menu = DB::table('dx_menu')->where('title', '=', 'Reports')->first();
                if ($rep_menu) {
                    DB::table('dx_menu')->insertGetId(['parent_id' => $rep_menu->id, 'title'=>$list_name, 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $rep_menu->id)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
                }
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
            \App\Libraries\DBHelper::deleteRegister('dx_timeoff_requests');
            DB::table('dx_objects')->where('db_name', '=', 'dx_timeoff_requests')->delete();
        });
    }
}

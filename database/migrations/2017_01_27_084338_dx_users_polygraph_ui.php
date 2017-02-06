<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;
use Illuminate\Support\Facades\Config;

class DxUsersPolygraphUi extends Migration
{
    private $is_hr_ui = false;
    private $is_hr_role = false;
    
    private function checkUI_Role() {
        $list_id = Config::get('dx.employee_list_id', 0);
        
        $this->is_hr_ui = ($list_id > 0);   
        
        $this->is_hr_role  = (App::getLocale() == 'en');
    }
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->checkUI_Role();
        
        if (!$this->is_hr_role || !$this->is_hr_ui) {
            return;
        }
        
        DB::transaction(function () {
            $table_name = "dx_users_polygraph";
            $list_name = "Polygraph results";
            $item_name = "Polygraph result";

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
            
            //fix file field
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'file_guid')
                    ->delete();

            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'file_name')
                    ->update([
                        'type_id' =>  \App\Libraries\DBHelper::FIELD_TYPE_FILE,
                        'is_required' => 1
                    ]);
            
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'notes')
                    ->update([
                        'type_id' =>  \App\Libraries\DBHelper::FIELD_TYPE_LONG_TEXT,
                        'max_lenght' => 2000
                    ]);
            
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'result_date')
                    ->update([
                        'default_value' =>  '[NOW]',
                        'is_required' => 1
                    ]);
            
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
        
            // user rights
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
            DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR
            
            // make tab in employee profile form
            $form_id = DB::table('dx_forms')->where('list_id', '=', Config::get('dx.employee_list_id'))->first()->id;

            DB::table('dx_forms_tabs')->insert([
                'form_id'=>$form_id,
                'title' => 'Polygraph',
                'grid_list_id' => $list_id,
                'grid_list_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'user_id')->first()->id,
                'order_index' => (DB::table('dx_forms_tabs')->where('form_id', '=', $form_id)->max('order_index')+10)
            ]);
            
            // make menu
            $parent_id = DB::table('dx_menu')->insertGetId(['title'=>'Confidential', 'order_index' => 75, 'position_id' => 1, 'group_id' => 1]);
            
            DB::table('dx_menu')->insert(['parent_id' => $parent_id, 'title'=>$list_name, 'list_id'=>$list_id, 'order_index' => 10, 'position_id' => 1, 'group_id' => 1]);
            
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
        
        if (!$this->is_hr_role || !$this->is_hr_ui) {
            return;
        }
        
        DB::transaction(function () {
            \App\Libraries\DBHelper::deleteRegister('dx_users_polygraph');
            DB::table('dx_objects')->where('db_name', '=', 'dx_users_polygraph')->delete();
            DB::table('dx_menu')->where('title', '=', 'Confidential')->delete();
        });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxEmailsSentUi extends Migration
{
    private $table_name = "dx_emails_sent";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            
            $list_name = trans('db_' . $this->table_name . '.list_name');
            $item_name = trans('db_' . $this->table_name . '.item_name');

             // create register
            $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $this->table_name, 'title' => $list_name , 'is_history_logic' => 1]);
            $list_gen = new Structure\StructMethod_register_generate();
            $list_gen->obj_id = $obj_id;
            $list_gen->register_title = $list_name;
            $list_gen->form_title = $item_name;
            $list_gen->doMethod();

            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id;       

            // reorganize view fields - hide or remove unneeded
            \App\Libraries\DBHelper::removeFieldsFromAllViews($this->table_name, ['id'], true); // hide ID field                       
            \App\Libraries\DBHelper::removeFieldsFromAllForms($this->table_name, ['id', 'template_id'], false);
            
            // user rights
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1, 'is_import_rights' => 1, 'is_view_rights' => 1]); // Sys admins
            
            $parent_menu = DB::table('dx_menu')->where('fa_icon', '=','fa fa-cogs')->first();
            if ($parent_menu) {
                $arr_params = [
                    'menu_list_id' => $list_id, 
                    'list_title' => $list_name,
                    'menu_parent_id' => $parent_menu->id
                ];
                App\Libraries\DBHelper::makeMenu($arr_params);   
            }

            // Adjust fields
            $form = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
            
            App\Libraries\DBHelper::updateFormField($list_id, "user_id", ['row_type_id' => 2]);
            App\Libraries\DBHelper::updateFormField($list_id, "sent_time", ['row_type_id' => 2]);

            App\Libraries\DBHelper::removeFieldsFromAllViews($list_id, [                
                'mail_text',                
                'template_id'
            ], false);

            $empl_list_id = Config::get('dx.employee_list_id', 0);
            if ($empl_list_id) {         
                $fld_display = DB::table('dx_lists_fields')->where('list_id', '=', $empl_list_id)->where('db_name', '=', 'display_name')->first();
                
                DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'user_id')->update([
                    'rel_list_id' => $empl_list_id,
                    'rel_display_field_id' => $fld_display->id
                ]);
            }

            App\Libraries\DBHelper::setFieldType($list_id, "user_id", App\Libraries\DBHelper::FIELD_TYPE_LOOKUP);
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
            \App\Libraries\DBHelper::deleteRegister($this->table_name);
            DB::table('dx_objects')->where('db_name', '=', $this->table_name)->delete();
        });
    }
}

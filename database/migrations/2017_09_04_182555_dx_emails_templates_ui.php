<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxEmailsTemplatesUi extends Migration
{
    private $table_name = "dx_emails_templates";
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
            \App\Libraries\DBHelper::removeFieldsFromAllForms($this->table_name, ['id'], false);
            
            // user rights
            DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1, 'is_import_rights' => 1, 'is_view_rights' => 1]); // Sys admins
            
            $parent_menu = DB::table('dx_menu')->where('title', '=', trans('db_dx_menu.lbl_classifiers'))->first();
            $arr_params = [
                'menu_list_id' => $list_id, 
                'list_title' => $list_name,
                'menu_parent_id' => $parent_menu->id
            ];
            App\Libraries\DBHelper::makeMenu($arr_params);   
            
            // Adjust fields
            $form = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
            
            $tab_main_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_' . $this->table_name . '.tab_main'),
                'is_custom_data' => 1,
                'order_index' => 10
            ]);

            $tab_text_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_' . $this->table_name . '.tab_text'),
                'is_custom_data' => 1,
                'order_index' => 20
            ]);

            App\Libraries\DBHelper::updateFormField($list_id, "code", ['tab_id' => $tab_main_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "mail_subject", ['tab_id' => $tab_main_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "title_bg_color", ['tab_id' => $tab_main_id, 'row_type_id' => 2]);
            App\Libraries\DBHelper::updateFormField($list_id, "title_fore_color", ['tab_id' => $tab_main_id, 'row_type_id' => 2]);

            App\Libraries\DBHelper::updateFormField($list_id, "mail_text", ['tab_id' => $tab_text_id]);

            App\Libraries\DBHelper::removeFieldsFromAllViews($list_id, [                
                'title_bg_color',                
                'title_fore_color',               
                'mail_text',
            ], false);

            App\Libraries\DBHelper::setFieldType($list_id, "title_bg_color", App\Libraries\DBHelper::FIELD_TYPE_COLOR);
            App\Libraries\DBHelper::setFieldType($list_id, "title_fore_color", App\Libraries\DBHelper::FIELD_TYPE_COLOR);
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

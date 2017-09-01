<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class DxDocTemplatesUi extends Migration
{
    private $table_name = "dx_doc_templates";
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

            $tab_sett_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_' . $this->table_name . '.tab_template'),
                'is_custom_data' => 1,
                'order_index' => 20
            ]);

            App\Libraries\DBHelper::updateFormField($list_id, "description", ['tab_id' => $tab_main_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "file_name", ['tab_id' => $tab_main_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "numerator_id", ['tab_id' => $tab_main_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "title_file", ['tab_id' => $tab_main_id]);

            App\Libraries\DBHelper::updateFormField($list_id, "html_template", ['tab_id' => $tab_sett_id]);

            App\Libraries\DBHelper::removeFieldsFromAllViews($list_id, [                
                'description',                
                'numerator_id',               
                'html_template',
                'title_file',
                'file_name'
            ], false);

            App\Libraries\DBHelper::addJavaScriptToForm($this->table_name, '2017_09_01_dx_doc_templates.js', trans('db_' . $this->table_name . '.js_info'));

            App\Libraries\DBHelper::addHintTofield($list_id, "title_file", trans('db_' . $this->table_name . '.title_file_hint'));
            App\Libraries\DBHelper::addHintTofield($list_id, "html_template", trans('db_' . $this->table_name . '.html_template_hint'));
            App\Libraries\DBHelper::addHintTofield($list_id, "description", trans('db_' . $this->table_name . '.description_hint'));

            App\Libraries\DBHelper::setFieldType($list_id, "list_id", App\Libraries\DBHelper::FIELD_TYPE_LOOKUP);
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

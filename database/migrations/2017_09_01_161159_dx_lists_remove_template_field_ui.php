<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxListsRemoveTemplateFieldUi extends Migration
{
    private $table_name = "dx_lists";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {            
            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id; 
            
            App\Libraries\DBHelper::removeFieldCMS($list_id, '	template_name'); 
            
            $templ_list = DB::table('dx_lists')->where('list_title', '=', trans('db_dx_doc_templates.list_name'))->first();
            $form = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
            $templ_field = DB::table('dx_lists_fields')->where('list_id', '=', $templ_list->id)->where('db_name', '=', 'list_id')->first();
    
            DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_dx_lists.tab_templ'),
                'is_custom_data' => 0,
                'order_index' => DB::table('dx_forms_tabs')->where('form_id', '=', $form->id)->max('order_index') + 10,
                'grid_list_id' => $templ_list->id,
                'grid_list_field_id' => $templ_field->id
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
        //
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxPagesUi extends Migration
{
    private $table_name = "dx_pages";
    
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
            
            // Adjust fields
            $form = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();

            App\Libraries\DBHelper::removeFieldCMS($list_id, 'source_id'); 
            \App\Libraries\DBHelper::removeFieldsFromAllViews($this->table_name, ['id'], true); // hide ID field                       
            \App\Libraries\DBHelper::removeFieldsFromAllForms($this->table_name, ['id'], false);
            
            $tab_main_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_' . $this->table_name . '.tab_main'),
                'is_custom_data' => 1,
                'order_index' => 1
            ]);

            $tab_cont_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_' . $this->table_name . '.tab_content'),
                'is_custom_data' => 1,
                'order_index' => 2
            ]);

            App\Libraries\DBHelper::updateFormField($list_id, "title", ['row_type_id' => 2]);
            App\Libraries\DBHelper::updateFormField($list_id, "url_title", ['row_type_id' => 2]);

            App\Libraries\DBHelper::updateFormField($list_id, "html", ['tab_id' => $tab_cont_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "file_name", ['tab_id' => $tab_cont_id, 'row_type_id' => 2]);
            App\Libraries\DBHelper::updateFormField($list_id, "content_bg_color", ['tab_id' => $tab_cont_id, 'row_type_id' => 2]);

            App\Libraries\DBHelper::updateFormField($list_id, "is_active", ['tab_id' => $tab_main_id]);
            App\Libraries\DBHelper::updateFormField($list_id, "group_id", ['tab_id' => $tab_main_id]);

            DB::table('dx_lists_fields')
            ->where('list_id', '=', $list_id)
            ->where('db_name', '=', 'group_id')
            ->update([
                'is_required' => 1
            ]);

            DB::table('dx_lists_fields')
            ->where('list_id', '=', $list_id)
            ->where('db_name', '=', 'html')
            ->update([
                'is_required' => 0
            ]);

            DB::table('dx_lists_fields')
            ->where('list_id', '=', $list_id)
            ->where('db_name', '=', 'title')
            ->update([
                'title_list' => trans('db_' . $this->table_name . '.title'),
                'title_form' => trans('db_' . $this->table_name . '.title')
            ]);

            DB::table('dx_lists_fields')
            ->where('list_id', '=', $list_id)
            ->where('db_name', '=', 'is_active')
            ->update([
                'title_list' => trans('db_' . $this->table_name . '.is_active'),
                'title_form' => trans('db_' . $this->table_name . '.is_active')
            ]);

            DB::table('dx_lists_fields')
            ->where('list_id', '=', $list_id)
            ->where('db_name', '=', 'group_id')
            ->update([
                'title_list' => trans('db_' . $this->table_name . '.group_id'),
                'title_form' => trans('db_' . $this->table_name . '.group_id'),
                'hint' => trans('db_' . $this->table_name . '.group_id_hint')
            ]);

            DB::table('dx_forms_tabs')
            ->where('id', '=', 49)
            ->update([
                'title' => trans('db_' . $this->table_name . '.tab_roles')
            ]);

            DB::table('dx_forms')
            ->where('list_id', '=', $list_id)
            ->update([
                'title' => trans('db_' . $this->table_name . '.item_name')
            ]);

            DB::table('dx_views')
            ->where('list_id', '=', $list_id)
            ->update([
                'title' => trans('db_' . $this->table_name . '.list_name')
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
        DB::transaction(function () {            
            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id; 
            
            // Adjust fields
            $form = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
                
            App\Libraries\DBHelper::updateFormField($list_id, "title", ['row_type_id' => 1]);
            App\Libraries\DBHelper::updateFormField($list_id, "url_title", ['row_type_id' => 1]);

            App\Libraries\DBHelper::updateFormField($list_id, "html", ['tab_id' => null]);
            App\Libraries\DBHelper::updateFormField($list_id, "file_name", ['tab_id' => null, 'row_type_id' => 1]);
            App\Libraries\DBHelper::updateFormField($list_id, "content_bg_color", ['tab_id' => null, 'row_type_id' => 1]);

            App\Libraries\DBHelper::updateFormField($list_id, "is_active", ['tab_id' => null]);
            App\Libraries\DBHelper::updateFormField($list_id, "group_id", ['tab_id' => null]);

            DB::table('dx_forms_tabs')->where('form_id', '=', $form->id)->where('title', '=', trans('db_' . $this->table_name . '.tab_main'))->delete();
            DB::table('dx_forms_tabs')->where('form_id', '=', $form->id)->where('title', '=', trans('db_' . $this->table_name . '.tab_content'))->delete();

            DB::table('dx_lists_fields')
                ->where('list_id', '=', $list_id)
                ->where('db_name', '=', 'group_id')
                ->update([
                    'is_required' => 0
                ]);
        });
    }
}

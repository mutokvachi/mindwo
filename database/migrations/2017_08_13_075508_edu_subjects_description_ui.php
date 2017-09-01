<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduSubjectsDescriptionUi extends EduMigration
{
    private $table_name = "edu_subjects";
     
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function edu_up()
    {
        DB::transaction(function () {
            
            $list = DB::table('dx_lists')->where('list_title', '=', trans('db_' . $this->table_name . '.list_name'))->first();
            
            $form = DB::table('dx_forms')->where('list_id', '=', $list->id)->first();
            
            $tab_descr_id = DB::table('dx_forms_tabs')
                    ->where('form_id', '=', $form->id)
                    ->where('title', '=', trans('db_' . $this->table_name . '.tab_descr'))
                    ->first()
                    ->id;
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list->id,
                'db_name' => 'purpose',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_HTML,
                'title_list' => trans('db_' . $this->table_name . '.purpose'),
                'title_form' => trans('db_' . $this->table_name . '.purpose'),
            ]);
            App\Libraries\DBHelper::addFieldToForm($list->id, $fld_id, ['order_index' => 10, 'tab_id' => $tab_descr_id]); 
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list->id,
                'db_name' => 'target_audience',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_HTML,
                'title_list' => trans('db_' . $this->table_name . '.target_audience'),
                'title_form' => trans('db_' . $this->table_name . '.target_audience'),
            ]);                                   
            App\Libraries\DBHelper::addFieldToForm($list->id, $fld_id, ['order_index' => 20, 'tab_id' => $tab_descr_id]);  
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list->id,
                'db_name' => 'prerequisites',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_HTML,
                'title_list' => trans('db_' . $this->table_name . '.prerequisites'),
                'title_form' => trans('db_' . $this->table_name . '.prerequisites'),
            ]);                                   
            App\Libraries\DBHelper::addFieldToForm($list->id, $fld_id, ['order_index' => 30, 'tab_id' => $tab_descr_id]);  

            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list->id,
                'db_name' => 'topics',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_HTML,
                'title_list' => trans('db_' . $this->table_name . '.topics'),
                'title_form' => trans('db_' . $this->table_name . '.topics'),
            ]);                                   
            App\Libraries\DBHelper::addFieldToForm($list->id, $fld_id, ['order_index' => 40, 'tab_id' => $tab_descr_id]);  

            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list->id,
                'db_name' => 'benefits',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_HTML,
                'title_list' => trans('db_' . $this->table_name . '.benefits'),
                'title_form' => trans('db_' . $this->table_name . '.benefits'),
            ]);                                   
            App\Libraries\DBHelper::addFieldToForm($list->id, $fld_id, ['order_index' => 50, 'tab_id' => $tab_descr_id]);  

            $tab_main_id = DB::table('dx_forms_tabs')
                    ->where('form_id', '=', $form->id)
                    ->where('title', '=', trans('db_' . $this->table_name . '.tab_main'))
                    ->first()
                    ->id;
            
            $coord_list = DB::table('dx_lists')->where('list_title', '=', trans('db_dx_users.list_title_edu'))->first();
            $coord_display = DB::table('dx_lists_fields')->where('list_id', '=', $coord_list->id)->where('db_name', '=', 'full_name_code')->first();
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list->id,
                'db_name' => 'coordinator_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_RELATED,
                'title_list' => trans('db_' . $this->table_name . '.coordinator_id'),
                'title_form' => trans('db_' . $this->table_name . '.coordinator_id'),
                'rel_list_id' => $coord_list->id,
                'rel_display_field_id' => $coord_display->id
            ]);                                 
            App\Libraries\DBHelper::addFieldToForm($list->id, $fld_id, ['tab_id' => $tab_main_id]);  
            \App\Libraries\DBHelper::reorderFormField($list->id, 'coordinator_id', 'is_published');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function edu_down()
    {
        DB::transaction(function () {
            
            $list = DB::table('dx_lists')->where('list_title', '=', trans('db_' . $this->table_name . '.list_name'))->first();                    
                        
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'purpose');
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'target_audience');
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'prerequisites');
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'topics');
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'benefits');
            
        });
    }
}

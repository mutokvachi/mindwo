<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class DxUsersDescriptionUi extends EduMigration
{
    private $table_name = "dx_users";
     
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function edu_up()
    {
        DB::transaction(function () {
            
            $list = DB::table('dx_lists')->where('list_title', '=', trans('db_' . $this->table_name . '.list_title_teacher'))->first();
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list->id,
                'db_name' => 'introduction',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_HTML,
                'title_list' => trans('db_' . $this->table_name . '.introduction'),
                'title_form' => trans('db_' . $this->table_name . '.introduction'),
                'hint' => trans('db_' . $this->table_name . '.introduction_hint'),
            ]);
            
            $form = DB::table('dx_forms')->where('list_id', '=', $list->id)->first();
            
            $tab_descr_id = DB::table('dx_forms_tabs')->insertGetId([
                'form_id' => $form->id,
                'title' => trans('db_' . $this->table_name . '.tab_description'),
                'is_custom_data' => 1,
                'order_index' => 15
            ]);
            
            App\Libraries\DBHelper::addFieldToForm($list->id, $fld_id, ['order_index' => 10, 'tab_id' => $tab_descr_id]); 
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list->id,
                'db_name' => 'experience',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_HTML,
                'title_list' => trans('db_' . $this->table_name . '.experience'),
                'title_form' => trans('db_' . $this->table_name . '.experience'),
                'hint' => trans('db_' . $this->table_name . '.experience_hint'),
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($list->id, $fld_id, ['order_index' => 20, 'tab_id' => $tab_descr_id]);  
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list->id,
                'db_name' => 'education',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_HTML,
                'title_list' => trans('db_' . $this->table_name . '.education'),
                'title_form' => trans('db_' . $this->table_name . '.education'),
                'hint' => trans('db_' . $this->table_name . '.education_hint'),
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($list->id, $fld_id, ['order_index' => 30, 'tab_id' => $tab_descr_id]);  

            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list->id,
                'db_name' => 'additional_info',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_HTML,
                'title_list' => trans('db_' . $this->table_name . '.additional_info'),
                'title_form' => trans('db_' . $this->table_name . '.additional_info'),
                'hint' => trans('db_' . $this->table_name . '.additional_info_hint'),
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($list->id, $fld_id, ['order_index' => 40, 'tab_id' => $tab_descr_id]);  

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
            
            $list = DB::table('dx_lists')->where('list_title', '=', trans('db_' . $this->table_name . '.list_title_teacher'))->first();                    
                        
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'additional_info');
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'education');
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'experience');
            App\Libraries\DBHelper::removeFieldCMS($list->id, 'introduction');
            
        });
    }
}

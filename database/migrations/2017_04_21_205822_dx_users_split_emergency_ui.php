<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersSplitEmergencyUi extends Migration
{
    private $is_hr_ui = false;
    private $hr_role_id = 0;
    
    private function checkUI_Role() {
        $list_id = Config::get('dx.employee_list_id', 0);
        
        $this->is_hr_ui = ($list_id > 0);   
        
        $row_role  = DB::table('dx_roles')->where('title', '=', 'HR')->first();
        
        if ($row_role) {
            $this->hr_role_id = $row_role->id;
        }
    }
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->checkUI_Role();
        
        if ($this->hr_role_id == 0 || !$this->is_hr_ui) {
            return;
        }
        
        $list_id = Config::get('dx.employee_list_id', 0);
            
        $type_list = App\Libraries\DBHelper::getListByTable('dx_users_emergency_types');

        if (!$type_list) {
            return;
        }

        $type_list_id = $type_list->id;
        $form_id = DB::table('dx_forms')->where('list_id', '=', $list_id)->first()->id; 
        
        $tab_row = DB::table('dx_forms_tabs')->where('form_id', '=', $form_id)->where('title', '=', trans('db_dx_users.tab_contact'))->first();
        
        if (!$tab_row) {
            return;
        }
        
        $tab_id = $tab_row->id; 
            
        DB::transaction(function () use ($list_id, $type_list_id, $form_id, $tab_id)
        {
            
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'emergency1_type_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_RELATED,
                'title_list' => trans('db_dx_users.emergency1_type_id'),
                'title_form' => trans('db_dx_users.emergency1_type_id'),
                'rel_list_id' => $type_list_id,
                'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $type_list_id)->where('db_name', '=', 'title')->first()->id,
            ]);    
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['tab_id' => $tab_id, 'row_type_id' => 3, 'order_index' => 162, 'group_label' => trans('db_dx_users.field_group_title')]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'emergency1_name',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => trans('db_dx_users.emergency1_name'),
                'title_form' => trans('db_dx_users.emergency1_name'),
                'max_lenght' => 100
            ]);                           
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['tab_id' => $tab_id, 'row_type_id' => 3, 'order_index' => 163]);

            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'emergency1_phone',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => trans('db_dx_users.emergency1_phone'),
                'title_form' => trans('db_dx_users.emergency1_phone'),
                'max_lenght' => 100
            ]);                           
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['tab_id' => $tab_id, 'row_type_id' => 3, 'order_index' => 164]);

            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'emergency2_type_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_RELATED,
                'title_list' => trans('db_dx_users.emergency2_type_id'),
                'title_form' => trans('db_dx_users.emergency2_type_id'),
                'rel_list_id' => $type_list_id,
                'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $type_list_id)->where('db_name', '=', 'title')->first()->id,
            ]);    
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['tab_id' => $tab_id, 'row_type_id' => 3, 'order_index' => 165]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'emergency2_name',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => trans('db_dx_users.emergency2_name'),
                'title_form' => trans('db_dx_users.emergency2_name'),
                'max_lenght' => 100
            ]);                           
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['tab_id' => $tab_id, 'row_type_id' => 3, 'order_index' => 166]);

            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'emergency2_phone',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => trans('db_dx_users.emergency2_phone'),
                'title_form' => trans('db_dx_users.emergency2_phone'),
                'max_lenght' => 100
            ]);                           
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['tab_id' => $tab_id, 'row_type_id' => 3, 'order_index' => 167]);
            
            DB::table('dx_lists_fields')
                ->where('list_id', '=', $list_id)
                ->where('db_name', '=', 'emergency_contacts')
                ->update([
                    'title_list' => trans('db_dx_users.emergency_notes'),
                    'title_form' => trans('db_dx_users.emergency_notes'),
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
        $this->checkUI_Role();
        
        if ($this->hr_role_id == 0 || !$this->is_hr_ui) {
            return;
        }        
        
        $list_id = Config::get('dx.employee_list_id', 0);;
        
        DB::transaction(function () use ($list_id){            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'emergency1_type_id');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'emergency1_name');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'emergency1_phone');
            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'emergency2_type_id');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'emergency2_name');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'emergency2_phone');
        });
    }
}

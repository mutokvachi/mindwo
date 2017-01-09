<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxUsersUiAddSupervise extends Migration
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
        
        if (!$this->is_hr_ui) {
            return;
        }
        
        DB::transaction(function () {            
            // pievieno lauku CMSā
            $list_id = Config::get('dx.employee_list_id');            
           
            $rel_list_id = App\Libraries\DBHelper::getListByTable('dx_supervise')->id;
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'dx_supervise_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_RELATED,
                'rel_list_id' => $rel_list_id,
                'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $rel_list_id)->where('db_name', '=', 'title')->first()->id,
                'title_list' => 'Supervision domain',
                'title_form' => 'Supervision domain',
                'is_required' => 1
            ]);
        
            App\Libraries\DBHelper::addFieldToFormTab($list_id, $fld_id, 'General', 312);
            
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
        
        if (!$this->is_hr_ui) {
            return;
        }
        
        DB::transaction(function () {
            $list_id = Config::get('dx.employee_list_id');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'dx_supervise_id');
        });
    }
}

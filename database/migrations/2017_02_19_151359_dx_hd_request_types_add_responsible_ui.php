<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Config;

class DxHdRequestTypesAddResponsibleUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
           
            $list_id = App\Libraries\DBHelper::getListByTable('dx_hd_request_types')->id;
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'resp_admin_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_LOOKUP,
                'title_list' => 'Administrators',
                'title_form' => 'Administrators',
                'rel_list_id' => Config::get('dx.employee_list_id'),
                'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', Config::get('dx.employee_list_id'))->where('db_name', '=', 'display_name')->first()->id,
            ]);
        
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, []); 
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'resp_junior_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_LOOKUP,
                'title_list' => 'Jaunākais programmētājs',
                'title_form' => 'Jaunākais programmētājs',
                'rel_list_id' => Config::get('dx.employee_list_id'),
                'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', Config::get('dx.employee_list_id'))->where('db_name', '=', 'display_name')->first()->id,
            ]);
        
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, []); 
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'resp_programmer_id',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_LOOKUP,
                'title_list' => 'Programmētājs',
                'title_form' => 'Programmētājs',
                'rel_list_id' => Config::get('dx.employee_list_id'),
                'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', Config::get('dx.employee_list_id'))->where('db_name', '=', 'display_name')->first()->id,
            ]);
        
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, []); 
            
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
            $list_id = App\Libraries\DBHelper::getListByTable('dx_hd_request_types')->id;
            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'resp_admin_id');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'resp_junior_id');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'resp_programmer_id');

        });
    }
}

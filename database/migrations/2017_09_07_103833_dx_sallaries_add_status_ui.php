<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\HRMigration;

class DxSallariesAddStatusUi extends HRMigration
{
    private $table_name = "dx_users_salaries";
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function hr_up()
    {
        DB::transaction(function () {            
            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id; 
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'aggr_status',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => 'Status',
                'title_form' => 'Status',
                'formula' => "case when [Valid till] < date(now()) then 'Old' else case when [Valid from] > date(now()) then 'Future' else 'Actual' end end "
            ]);
            
            $view = DB::table('dx_views')->where('list_id', '=', $list_id)->first();

            App\Libraries\DBHelper::addFieldToView($list_id, $view->id, $fld_id);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function hr_down()
    {
        DB::transaction(function () {            
            // get list
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id; 
            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'aggr_status');
        });
    }
}

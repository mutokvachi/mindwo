<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class TimeoffRequestsUiJs extends Migration
{
    private $js_title = 'Calculate timeoff request detailed textual info';
    private $table_name = 'dx_timeoff_requests';
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id;
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'request_details',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => 'Request details',
                'title_form' => 'Request details',
            ]);
            
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['is_hidden' => 1]);
            
            // add special JavaScript
            \App\Libraries\DBHelper::addJavaScriptToForm($this->table_name, '2016_12_14_timeoff_requests.js', $this->js_title);
            
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
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id;
            App\Libraries\DBHelper::dropField($list_id, 'request_details');
            App\Libraries\DBHelper::removeJavaScriptFromForm($this->table_name, $this->js_title);
        });
    }
}

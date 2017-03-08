<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxHdRequestsAddEstimatedUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
           
            $list_id = App\Libraries\DBHelper::getListByTable('dx_hd_requests')->id;
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'planned_finish',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_DATETIME,
                'title_list' => 'Plānots izpildīt',
                'title_form' => 'Plānots izpildīt',
            ]);
        
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 105, 'row_type_id' => 2]);
            
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'responsible_empl_id')
                                                ->first()->id)
                    ->update([
                        'row_type_id' => 2
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
            $list_id = App\Libraries\DBHelper::getListByTable('dx_hd_requests')->id;
            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'planned_finish');
            
            DB::table('dx_forms_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('field_id', '=', DB::table('dx_lists_fields')
                                                ->where('list_id', '=', $list_id)
                                                ->where('db_name', '=', 'responsible_empl_id')
                                                ->first()->id)
                    ->update([
                        'row_type_id' => 1
                    ]);

        });
    }
}

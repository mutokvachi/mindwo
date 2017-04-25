<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxHdRequestsAddFilesUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $obj= DB::table('dx_objects')->where('db_name', '=', 'dx_hd_requests')->first();
        
        if (!$obj) {
            return;
        }
        
        $obj_id = $obj->id;
        
        DB::transaction(function () use ($obj_id){
            
            $lists = DB::table('dx_lists')->where('object_id', '=', $obj_id)->get();
            
            foreach ($lists as $list) {
                $list_id = $list->id;
                
                $fld_id = DB::table('dx_lists_fields')->insertGetId([
                    'list_id' => $list_id,
                    'db_name' => 'file2_name',
                    'type_id' => App\Libraries\DBHelper::FIELD_TYPE_FILE,
                    'title_list' => 'Papildus datne 1',
                    'title_form' => 'Papildus datne 1'
                ]);

                App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 70, 'row_type_id' => 1]);

                $fld_id = DB::table('dx_lists_fields')->insertGetId([
                    'list_id' => $list_id,
                    'db_name' => 'file3_name',
                    'type_id' => App\Libraries\DBHelper::FIELD_TYPE_FILE,
                    'title_list' => 'Papildus datne 2',
                    'title_form' => 'Papildus datne 2'
                ]);

                App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 75, 'row_type_id' => 1]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $obj= DB::table('dx_objects')->where('db_name', '=', 'dx_hd_requests')->first();
        
        if (!$obj) {
            return;
        }
        
        $obj_id = $obj->id;
        
        DB::transaction(function () use ($obj_id){
            
            $lists = DB::table('dx_lists')->where('object_id', '=', $obj_id)->get();
            
            foreach ($lists as $list) {        
                $list_id = $list->id;
                
                App\Libraries\DBHelper::removeFieldCMS($list_id, 'file2_name');  
                App\Libraries\DBHelper::removeFieldCMS($list_id, 'file3_name');  
            }
        });
    }
}

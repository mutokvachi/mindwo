<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxHdAddMultiListIdUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $arr = $this->getTables();        
        
        DB::transaction(function () use ($arr){
            foreach($arr as $tbl) {
                DB::table('dx_objects')->where('db_name', '=', $tbl)->update(['is_multi_registers' => 1]);
                
                $list = \App\Libraries\DBHelper::getListByTable($tbl);
                
                if ($list) {
                    DB::table($tbl)->update(['multi_list_id' => $list->id]);
                }
            }            
            
            $obj = DB::table('dx_objects')->where('db_name', '=', 'dx_hd_requests')->first();
            
            if ($obj) {
                $list_main = DB::table('dx_lists')->where('object_id', '=', $obj->id)->where('list_title', '=', 'IT pieteikumi')->first();
                
                if ($list_main) {
                    DB::table('dx_hd_requests')->update(['list_id' => $list_main->id]);                
                
                    $all_lists = DB::table('dx_lists')->where('object_id', '=', $obj->id)->get();

                    foreach($all_lists as $list) {
                        $fld_id = DB::table('dx_lists_fields')->insertGetId([
                            'list_id' => $list->id,
                            'db_name' => 'list_id',
                            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_INT,
                            'title_list' => 'Reģistra ID',
                            'title_form' => 'Reģistra ID',
                            'operation_id' => 1,
                            'criteria' => $list_main->id,
                            'default_value' => $list_main->id
                        ]);

                        App\Libraries\DBHelper::addFieldToForm($list->id, $fld_id, ['is_hidden' => 1]);            
                    }
                }
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
       $arr = $this->getTables();        
        
        DB::transaction(function () use ($arr){
            foreach($arr as $tbl) {
                DB::table('dx_objects')->where('db_name', '=', $tbl)->update(['is_multi_registers' => 0]);
                
                $list = \App\Libraries\DBHelper::getListByTable($tbl);
                
                if ($list) {
                    DB::table($tbl)->update(['multi_list_id' => null]);
                }
            }
                            
            DB::table('dx_hd_requests')->update(['list_id' => null]);
            
            $obj = DB::table('dx_objects')->where('db_name', '=', 'dx_hd_requests')->first();
            
            if ($obj) {            
                
                $all_lists = DB::table('dx_lists')->where('object_id', '=', $obj->id)->get();

                foreach($all_lists as $list) {
                    App\Libraries\DBHelper::removeFieldCMS($list->id, 'list_id');          
                }
                
            }
            
            
        });
    }
    
    private function getTables() {
        return ['dx_hd_inner_types', 'dx_hd_priorities', 'dx_hd_request_types', 'dx_hd_statuses'];
    }
}

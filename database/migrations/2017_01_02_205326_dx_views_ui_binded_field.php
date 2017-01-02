<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxViewsUiBindedField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {            
            
            $list = App\Libraries\DBHelper::getListByTable("dx_views");            
                        
            $fld_id = DB::table('dx_lists_fields')->where('list_id', '=', $list->id)->where('db_name', '=', 'field_id')->first()->id;
        
            DB::table('dx_lists_fields')->where('id', '=', 29)->update(['binded_field_id' => $fld_id, 'binded_rel_field_id' => 17]);
            
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
            $list = App\Libraries\DBHelper::getListByTable("dx_views");            
                        
            $fld_id = DB::table('dx_lists_fields')->where('list_id', '=', $list->id)->where('db_name', '=', 'field_id')->first()->id;
        
            DB::table('dx_lists_fields')->where('id', '=', 29)->update(['binded_field_id' => null, 'binded_rel_field_id' => null]);
 
        });
    }
}

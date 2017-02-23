<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxHdRequestTypesUiTree extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            $table_name = "dx_hd_request_types";
            $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;  

            $parent_id =  DB::table('dx_lists_fields')
                ->where('list_id', '=', $list_id)
                ->where('db_name', '=', 'parent_id')
                ->first()
                ->id;
            
            DB::table('dx_lists_fields')
                ->where('list_id', '=', $list_id)
                ->where('db_name', '=', 'parent_id')
                ->update([
                    'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_MULTILEVEL,
                    'rel_parent_field_id' => $parent_id
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
        //
    }
}

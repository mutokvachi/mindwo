<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxFieldTypesAddSkype extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function (){
            DB::table('dx_field_types')->insert([
               'id' => 19,
               'title' => 'Skype',
               'is_max_lenght' => 1,
               'sys_name' => 'skype',
               'height_px' => 34
            ]);
            
            DB::table('dx_lists_fields')->where('db_name', '=', 'skype')->update(['type_id' => 19]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::transaction(function () use ($list_id){
            DB::table('dx_field_types')->where('id', '=', 19)->delete();
            DB::table('dx_lists_fields')->where('db_name', '=', 'skype')->update(['type_id' => 1]);
        });
    }
}

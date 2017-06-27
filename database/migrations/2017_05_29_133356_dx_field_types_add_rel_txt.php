<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxFieldTypesAddRelTxt extends Migration
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
                'id' => 22,
                'title' => trans('db_dx_field_types.rel_txt'),
                'sys_name' => 'rel_txt'
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
        DB::transaction(function (){
            DB::table('dx_field_types')->where('id', '=', 22)->delete();
        });
    }
}

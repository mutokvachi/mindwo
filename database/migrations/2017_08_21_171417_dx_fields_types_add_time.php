<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxFieldsTypesAddTime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_field_types')->insert([
            'id' => 23,
            'title' => trans('db_dx_field_types.time'),
            'sys_name' => 'time',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_field_types')->where('id', '=', 23)->delete();
    }
}

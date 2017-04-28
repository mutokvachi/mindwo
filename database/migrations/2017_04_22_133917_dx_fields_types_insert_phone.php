<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxFieldsTypesInsertPhone extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_field_types')->insert([
            'id' => 21,
            'title' => 'Phone',
            'is_max_lenght' => 1,
            'sys_name' => 'phone'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_field_types')->where('id', '=', 21)->delete();
    }
}

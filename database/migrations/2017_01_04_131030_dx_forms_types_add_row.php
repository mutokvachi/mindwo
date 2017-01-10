<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxFormsTypesAddRow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_forms_types')->insert(['id'=>3, 'title'=>'Employee profile']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_forms_types')->where('id', '=', 3)->delete();
    }
}

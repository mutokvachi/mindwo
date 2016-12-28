<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxFieldRepresentDataResponsible extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_field_represent')->insert(['id'=>10, 'title' => trans('dx_field_represent.responsible_employee')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_field_represent')->where('id', '=', 10)->delete();
    }
}

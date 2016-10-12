<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPasswordField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_field_types')->insert(['title' => 'Parole', 'sys_name' => 'password', 'height_px' => 21]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_field_types')->where('sys_name', '=', 'password')->delete();
    }
}

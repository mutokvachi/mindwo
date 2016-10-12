<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RedefinePasswordFieldInRegister extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_lists_fields')
            ->where('list_id', '=', 21)
            ->where('db_name', '=', 'passw')
            ->update(['db_name' => 'password', 'type_id' => 16]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_lists_fields')
            ->where('list_id', '=', 21)
            ->where('db_name', '=', 'password')
            ->update(['db_name' => 'passw', 'type_id' => 1]);
    }
}

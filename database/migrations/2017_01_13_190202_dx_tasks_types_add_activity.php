<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxTasksTypesAddActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_tasks_types')->insert(['id' => 8, 'title' => trans('db_dx_tasks_types.custom_activity')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_tasks_types')->where('id', '=', 8)->delete();
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTaskStatusDelegate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_tasks_statuses')->insert(['id' => 5, 'title' => 'Deleģēts']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_tasks_statuses')->where('id', '=', 5)->delete();
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPerformerDepLeader extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_tasks_perform')->insert(['title' => 'Departamenta vadītājs', 'code' => 'DEP_LEADER']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_tasks_perform')->where('code', '=', 'DEP_LEADER')->delete();
    }
}

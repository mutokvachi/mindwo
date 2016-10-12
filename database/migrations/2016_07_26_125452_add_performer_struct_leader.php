<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPerformerStructLeader extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_tasks_perform')->insert(['title' => 'Struktūrvienības vadītājs', 'code' => 'STRUCT_LEADER']);
        DB::table('dx_tasks_perform')->insert(['title' => 'Darbinieks no dokumenta', 'code' => 'DOC_USER']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('dx_tasks_perform')->where('code', '=', 'STRUCT_LEADER')->delete();
        DB::table('dx_tasks_perform')->where('code', '=', 'DOC_USER')->delete();
    }
}

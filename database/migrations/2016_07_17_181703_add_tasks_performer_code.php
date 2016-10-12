<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTasksPerformerCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('dx_tasks_perform', function (Blueprint $table) {
            $table->string('code', 50)->nullable()->comment = "Kods";
            
            $table->unique('code');
        });
        
        DB::table('dx_tasks_perform')->where('id', '=', 1)->update(['code' => 'EMPLOYEE']);
        DB::table('dx_tasks_perform')->where('id', '=', 2)->update(['code' => 'MANAGER']);
        DB::table('dx_tasks_perform')->where('id', '=', 3)->update(['code' => 'CREATOR']);
        
        DB::table('dx_tasks_perform')->insert(['title' => 'Loma', 'code' => 'ROLE']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_tasks_perform', function (Blueprint $table) {
            $table->dropColumn(['code']);
        });
    }
}

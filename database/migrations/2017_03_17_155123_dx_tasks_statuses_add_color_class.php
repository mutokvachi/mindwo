<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxTasksStatusesAddColorClass extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_tasks_statuses', function (Blueprint $table) {
            $table->string('color', 50)->nullable()->comment = trans('db_dx_tasks_statuses.color');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_tasks_statuses', function (Blueprint $table) {
            $table->dropColumn(['color']);
        });
    }
}

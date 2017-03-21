<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxTasksStatusesAddIsRevocable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_tasks_statuses', function (Blueprint $table) {
            $table->boolean('is_revocable')->default(false)->comment = trans('db_dx_tasks_statuses.is_revocable');
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
            $table->dropColumn(['is_revocable']);
        });
    }
}

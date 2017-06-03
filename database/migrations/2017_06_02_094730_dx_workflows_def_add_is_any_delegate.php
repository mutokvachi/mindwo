<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxWorkflowsDefAddIsAnyDelegate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_workflows_def', function (Blueprint $table) {
            $table->boolean('is_any_delegate')->nullable()->default(false)->comment = trans('db_dx_workflows_def.is_any_delegate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_lists', function (Blueprint $table) {
            $table->dropColumn(['is_any_delegate']);
        });
    }
}

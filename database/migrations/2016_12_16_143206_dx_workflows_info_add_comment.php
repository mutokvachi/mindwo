<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxWorkflowsInfoAddComment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_workflows_info', function (Blueprint $table) {
            $table->string('comment', 4000)->nullable()->comment = "Comment";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_workflows_info', function (Blueprint $table) {
            $table->dropColumn(['comment']);
        });
    }
}

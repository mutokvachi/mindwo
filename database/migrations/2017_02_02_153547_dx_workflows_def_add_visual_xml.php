<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Adds column which conatins XML data for workflow's visualization
 */
class DxWorkflowsDefAddVisualXml extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_workflows_def', function (Blueprint $table) {
            $table->text('visual_xml')->nullable()->comment = "XML data for visualization";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_workflows_def', function (Blueprint $table) {
            $table->dropColumn(['visual_xml']);
        });
    }
}

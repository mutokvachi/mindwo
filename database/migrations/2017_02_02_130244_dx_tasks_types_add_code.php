<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Adds new column "code" which contains code which identifies task type. Fills code columns.
 */
class DxTasksTypesAddCode extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::table('dx_tasks_types', function (Blueprint $table) {
            $table->string('code', 10)->nullable()->comment = "Code";
        });

        $taskType = \App\Models\Workflow\TaskType::where('title', 'Approve')->first();
        if ($taskType) {
            $taskType->code = 'APPR';
            $taskType->save();
        }

        $taskType = \App\Models\Workflow\TaskType::where('title', 'Execute')->first();
        if ($taskType) {
            $taskType->code = 'EXEC';
            $taskType->save();
        }

        $taskType = \App\Models\Workflow\TaskType::where('title', 'Supplement and approve')->first();
        if ($taskType) {
            $taskType->code = 'SUPP';
            $taskType->save();
        }

        $taskType = \App\Models\Workflow\TaskType::where('title', 'Set value')->first();
        if ($taskType) {
            $taskType->code = 'SET';
            $taskType->save();
        }

        $taskType = \App\Models\Workflow\TaskType::where('title', 'Criterion')->first();
        if ($taskType) {
            $taskType->code = 'CRIT';
            $taskType->save();
        }

        $taskType = \App\Models\Workflow\TaskType::where('title', 'Information')->first();
        if ($taskType) {
            $taskType->code = 'INFO';
            $taskType->save();
        }

        $taskType = \App\Models\Workflow\TaskType::where('title', 'Criterion - is manually seted reconciliation')->first();
        if ($taskType) {
            $taskType->code = 'CRITM';
            $taskType->save();
        }

        $taskType = \App\Models\Workflow\TaskType::where('title', 'Custom activity')->first();
        if ($taskType) {
            $taskType->code = 'CACT';
            $taskType->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_tasks_types', function (Blueprint $table) {
            $table->dropColumn(['code']);
        });
    }
}

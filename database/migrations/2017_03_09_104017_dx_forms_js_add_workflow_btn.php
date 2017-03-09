<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Adds javascript which opens workflow in designer
 */
class DxFormsJsAddWorkflowBtn extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Libraries\DBHelper::addJavaScriptToForm('dx_workflows_def', '2017_03_09_dx_forms_js_workflow_btn.js', 'Workflow Designer Button');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $workflow_steps_list_id = App\Libraries\DBHelper::getListByTable('dx_workflows_def')->id;

        $form = \DB::table('dx_forms')
                ->select('id')
                ->where('list_id', $workflow_steps_list_id)
                ->first();

        if ($form) {
            \DB::table('dx_forms_js')->where('form_id', $form->id)
                    ->where('title', 'Workflow Designer Button')
                    ->delete();
        }
    }
}

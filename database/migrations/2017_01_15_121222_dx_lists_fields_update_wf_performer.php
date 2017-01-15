<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxListsFieldsUpdateWfPerformer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('dx_lists_fields')->where('id','=',712)->update([
           'formula' => "case when [Task type] in (4, 5, 8) then '" . trans('workflow.performer_system') . "' else '" . trans('workflow.performer_empl') . "' end"
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

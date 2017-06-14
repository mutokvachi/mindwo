<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxListsFieldsAddValidation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            $act_id = DB::table('dx_actions')->insertGetId([
                    'title' => trans('db_dx_lists_fields.action_validation'),
                    'code' => 'VALIDATE_LIST_FIELD'
                ]);
            
                $list_id = App\Libraries\DBHelper::getListByTable('dx_lists_fields')->id;
                $form_id = DB::table('dx_forms')->where('list_id','=',$list_id)->first()->id;
                
                DB::table('dx_forms_actions')->insert([
                    'form_id' => $form_id,
                    'action_id' => $act_id,
                    'is_after_save' => 0
                ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::transaction(function () {
            $act_id = DB::table('dx_actions')->where('code', '=', 'VALIDATE_LIST_FIELD')->first()->id;
                            
            DB::table('dx_forms_actions')->where('action_id', '=', $act_id)->delete();
            DB::table('dx_actions')->where('id', '=', $act_id)->delete();
        });
    }
}

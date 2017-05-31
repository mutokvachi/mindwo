<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxCryptoRegenUiLookupFix extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            $list_id = App\Libraries\DBHelper::getListByTable('dx_crypto_regen')->id;

            // ADD RELATION FOR CREATED USER FIELD
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'created_user_id')
                    ->update([
                        'rel_list_id' => Config::get('dx.employee_list_id'),
                        'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', Config::get('dx.employee_list_id'))->where('db_name', '=', 'display_name')->first()->id
            ]);

            // REMOVES MASTER KEY FIELD FROM FORM
            \App\Libraries\DBHelper::removeFieldsFromAllForms('dx_crypto_regen', ['master_key'], true);

            // SETS MASTER KEY GROUP FIELD AS READ ONLY
            $list_field = DB::table('dx_lists_fields')->where('list_id', $list_id)
                    ->where('db_name', 'master_key_group_id')
                    ->first();

            DB::table('dx_forms_fields')->where('list_id', $list_id)
                    ->where('field_id', $list_field->id)
                    ->update(['is_readonly' => 1]);
            
            // ADD CREATED USER TO VIEW
            $list_view = DB::table('dx_views')->where('list_id', $list_id)
                    ->first();
            
            $list_field_created = DB::table('dx_lists_fields')->where('list_id', $list_id)
                    ->where('db_name', 'created_user_id')
                    ->first();

            DB::table('dx_views_fields')
                    ->insert(['list_id' => $list_id,
                        'view_id' => $list_view->id,
                        'field_id' => $list_field_created->id,
                        'width' => 100,
                        'order_index' => 70,
                        'align' => 'left',
                        'is_item_link' => 0]);
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
            $list_id = App\Libraries\DBHelper::getListByTable('dx_crypto_regen')->id;

            // REMOVES LOOKUP 
            DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'user_id')
                    ->update([
                        'rel_list_id' => null,
                        'rel_display_field_id' => null
            ]);

            // REMOVES READONLY FOR MASTER KEY GROUP
            $list_field = DB::table('dx_lists_fields')->where('list_id', $list_id)
                    ->where('db_name', 'master_key_group_id')
                    ->first();

            DB::table('dx_forms_fields')->where('list_id', $list_id)
                    ->where('field_id', $list_field->id)
                    ->update(['is_readonly' => 0]);

            // REMOVE CREATED USER FROM VIEW
            $list_view = DB::table('dx_views')->where('list_id', $list_id)
                    ->first();

            $list_field_created = DB::table('dx_lists_fields')->where('list_id', $list_id)
                    ->where('db_name', 'created_user_id')
                    ->first();

            DB::table('dx_views_fields')
                    ->where('list_id', $list_id)
                    ->where('view_id', $list_view->id)
                    ->where('field_id', $list_field_created->id)
                    ->delete();
        });
    }
}

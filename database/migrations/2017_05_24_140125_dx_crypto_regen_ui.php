<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxCryptoRegenUi extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            $this->createUI();
        });
    }

    /**
     * Creates UI for master key regeneration register
     */
    private function createUI()
    {
        $list_name = trans('crypto.db.master_key_regens');
        $item_name = trans('crypto.db.master_key_regen');
        $table_name = "dx_crypto_regen";

        // create register
        $obj = DB::table('dx_objects')->where('db_name', 'dx_crypto_regen')->first();

        if ($obj) {
            $obj_id = $obj->id;
        } else {
            $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'dx_crypto_regen', 'title' => $list_name, 'is_history_logic' => 1]);
        }

        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = $list_name;
        $list_gen->form_title = $item_name;
        $list_gen->doMethod();

        // get list
        $list_id = App\Libraries\DBHelper::getListByTable('dx_crypto_regen')->id;

        // reorganize view fields - hide or remove unneeded
        \App\Libraries\DBHelper::removeFieldsFromAllViews($table_name, ['master_key'], true);

        // Add created_user_id, created_time, modified_time columns to view 
        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'created_user_id',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_LOOKUP,
            'title_list' => trans('crypto.db.created_user'),
            'title_form' => trans('crypto.db.created_user')
        ]);
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 55, 'is_readonly' => 1]);

        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'created_time',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_DATETIME,
            'title_list' => trans('crypto.db.created_time'),
            'title_form' => trans('crypto.db.created_time')
        ]);
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 65, 'is_readonly' => 1]);

        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'modified_time',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_DATETIME,
            'title_list' => trans('crypto.db.modified_time'),
            'title_form' => trans('crypto.db.modified_time')
        ]);
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 75, 'is_readonly' => 1]);

        // user rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins

        $parent_menu = DB::table('dx_menu')->where('title', '=', 'System')->first();

        if ($parent_menu) {
            $crypto_menu_id = DB::table('dx_menu')->insertGetId(['parent_id' => $parent_menu->id, 'title' => trans('crypto.db.crypto'), 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $parent_menu->id)->max('order_index') + 10), 'group_id' => 1, 'position_id' => 1]);

            DB::table('dx_menu')->insertGetId(['parent_id' => $crypto_menu_id, 'title' => $list_name, 'list_id' => $list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $crypto_menu_id)->max('order_index') + 10), 'group_id' => 1, 'position_id' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table_name = "dx_crypto_regen";

        $list = App\Libraries\DBHelper::getListByTable($table_name);

        if (!$list) {
            return;
        }

        $list_id = $list->id;

        DB::transaction(function () use ($list_id, $table_name) {
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'created_user_id');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'created_time');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'modified_time');

            \App\Libraries\DBHelper::deleteRegister($table_name);
            DB::table('dx_objects')->where('db_name', '=', $table_name)->delete();
        });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxCryptoMasterkeyGroupsUi extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            $list_id_dx_crypto_masterkey_groups = $this->createMasterKeyGroupsReg();

            $list_id_dx_crypto_masterkeys = $this->createMasterKeyRegister();

            // make tab in employee profile form
            $form_id = DB::table('dx_forms')->where('list_id', '=', $list_id_dx_crypto_masterkey_groups)->first()->id;

            DB::table('dx_forms_tabs')->insert([
                'form_id' => $form_id,
                'title' => 'Users',
                'grid_list_id' => $list_id_dx_crypto_masterkeys,
                'grid_list_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_id_dx_crypto_masterkeys)->where('db_name', '=', 'master_key_group_id')->first()->id,
                'order_index' => (DB::table('dx_forms_tabs')->where('form_id', '=', $form_id)->max('order_index') + 10)
            ]);
        });
    }

    private function createMasterKeyGroupsReg()
    {
        $table_name = "dx_crypto_masterkey_groups";
        $list_name = "Master key groups";
        $item_name = "Master key group";

        // create register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $table_name, 'title' => $list_name, 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = $list_name;
        $list_gen->form_title = $item_name;
        $list_gen->doMethod();

        // get list
        $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;

        // reorganize view fields - hide or remove unneeded
        \App\Libraries\DBHelper::removeFieldsFromAllViews($table_name, ['id'], true); // hide ID field
        // user rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins

        return $list_id;
    }

    private function createMasterKeyRegister()
    {

        $table_name = "dx_crypto_masterkeys";
        $list_name = "Crypto users";
        $item_name = "Crypto user";

        // create register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $table_name, 'title' => $list_name, 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = $list_name;
        $list_gen->form_title = $item_name;
        $list_gen->doMethod();

        // get list
        $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;

        // reorganize view fields - hide or remove unneeded
        \App\Libraries\DBHelper::removeFieldsFromAllViews($table_name, ['id'], true); // hide ID field
        
        DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', 'user_id')
                    ->update([
                        'rel_list_id'=>Config::get('dx.employee_list_id'),
                        'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', Config::get('dx.employee_list_id'))->where('db_name', '=', 'display_name')->first()->id,
                        'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_LOOKUP
                    ]);
        
        // user rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins

        return $list_id;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::transaction(function () {
            \App\Libraries\DBHelper::deleteRegister('dx_crypto_masterkey_groups');
            DB::table('dx_objects')->where('db_name', '=', 'dx_crypto_masterkey_groups')->delete();

            \App\Libraries\DBHelper::deleteRegister('dx_crypto_masterkeys');
            DB::table('dx_objects')->where('db_name', '=', 'dx_crypto_masterkeys')->delete();
        });
    }
}

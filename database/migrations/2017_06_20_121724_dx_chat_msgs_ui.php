<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxChatMsgsUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         DB::transaction(function () {
             // Adds useless column so it would be possible to generate "dx_chats_msgs" register - for lookup it must have at least one string field
             Schema::table('dx_chats', function (Blueprint $table) {
                $table->string('name')->nullable();
             });

            $table_name = "dx_chats";
            $list_name = trans('forms.chat.chat');
            $item_name = trans('forms.chat.chat');

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

            \Log::info('adsad');

            $table_name = "dx_chats_msgs";
            $list_name = trans('forms.chat.db.chat_msgs');
            $item_name = trans('forms.chat.db.chat_msgs');

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
            \App\Libraries\DBHelper::deleteRegister('dx_chats_msgs');
            DB::table('dx_objects')->where('db_name', '=', 'dx_chats_msgs')->delete();

            \App\Libraries\DBHelper::deleteRegister('dx_chats');
            DB::table('dx_objects')->where('db_name', '=', 'dx_chats')->delete();

            Schema::table('dx_chats', function (Blueprint $table) {
                $table->dropColumn(['name']);
            });
        });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxListsAddMasterkeyId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_lists', function (Blueprint $table) {
            $table->integer('masterkey_group_id')->unsigned()->nullable()->comment = trans('crypto.db.master_key_group_title');
            
            $table->index('masterkey_group_id');            
            $table->foreign('masterkey_group_id')->references('id')->on('dx_crypto_masterkey_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_lists', function (Blueprint $table) {            
            $table->dropForeign(['masterkey_group_id']);
            $table->dropColumn(['masterkey_group_id']);
        });
    }
}

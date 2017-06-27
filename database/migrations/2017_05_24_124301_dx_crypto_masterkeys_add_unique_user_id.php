<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Adds unique index so that we can not add multiple same group master keys
 */
class DxCryptoMasterkeysAddUniqueUserId extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_crypto_masterkeys', function (Blueprint $table) {
            $table->unique(['user_id', 'master_key_group_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_crypto_masterkeys', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'master_key_group_id']);
        });
    }
}

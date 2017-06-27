<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Changes dx_crypto_masterkeys master key field to text type
 */
class DxCryptoMasterkeysChangeMasterKey extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Schema::table('dx_crypto_masterkeys', function (Blueprint $table) {
                $table->text('master_key')->change();
            });
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
            Schema::table('dx_crypto_masterkeys', function (Blueprint $table) {
                $table->binary('master_key')->change();
            });
        });
    }
}

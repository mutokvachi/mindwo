<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

/**
 * Add javascript which generates maste rkey when saving user to master key group
 */
class DxCryptoMasterkeysAddFormBtnUi extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            \App\Libraries\DBHelper::addJavaScriptToForm('dx_crypto_masterkeys', '2017_04_25_dx_crypto_masterkeys.js', 'Generates master key for user on save');
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
            \App\Libraries\DBHelper::removeJavaScriptFromForm('dx_crypto_masterkeys', 'Generates master key for user on save');
        });
    }
}

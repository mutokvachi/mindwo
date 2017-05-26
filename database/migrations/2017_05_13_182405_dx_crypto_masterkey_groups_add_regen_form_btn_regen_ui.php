<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxCryptoMasterkeyGroupsAddRegenFormBtnRegenUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            \App\Libraries\DBHelper::addJavaScriptToForm('dx_crypto_masterkey_groups', '2017_05_13_dx_crypto_masterkeys_group_regen.js', 'Regenerate master key');
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
            \App\Libraries\DBHelper::removeJavaScriptFromForm('dx_crypto_masterkey_groups', 'Regenerate master key');
        });
    }
}

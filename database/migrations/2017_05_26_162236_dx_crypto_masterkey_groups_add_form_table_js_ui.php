<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxCryptoMasterkeyGroupsAddFormTableJsUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
        public function up()
    {
        DB::transaction(function () {
            \App\Libraries\DBHelper::addJavaScriptToForm('dx_crypto_masterkey_groups', '2017_05_26_dx_crypto_masterkey_groups.js', 'Check master key user changes');
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
            \App\Libraries\DBHelper::removeJavaScriptFromForm('dx_crypto_masterkey_groups', 'Check master key user changes');
        });
    }
}

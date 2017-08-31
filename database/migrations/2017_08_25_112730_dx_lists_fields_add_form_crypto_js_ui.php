<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Adds JS for form save event on save checks if crypto settings is changed. If yes then endcrypt or decrypt existing data.
 */
class DxListsFieldsAddFormCryptoJsUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            \App\Libraries\DBHelper::addJavaScriptToForm('dx_lists_fields', '2017_08_25_dx_lists_fields_crypto_toggle.js', 'Encrypt/decrpyt data if encryption field toggled');
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
            \App\Libraries\DBHelper::removeJavaScriptFromForm('dx_lists_fields', 'Encrypt/decrpyt data if encryption field toggled');
        });
    }
}

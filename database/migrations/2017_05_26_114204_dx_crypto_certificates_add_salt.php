<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxCryptoCertificatesAddSalt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_crypto_certificates', function (Blueprint $table) {
            $table->string('salt', 50)->nullable()->comment = 'Salt';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_crypto_certificates', function (Blueprint $table) {
            $table->dropColumn(['salt']);
        });
    }
}

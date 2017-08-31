<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxCryptoRegenAddFieldId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('dx_crypto_regen', function (Blueprint $table) {
            $table->integer('field_id')->nullable()->comment = trans('crypto.db.field');
            
            $table->index('field_id');            
            $table->foreign('field_id')->references('id')->on('dx_lists_fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_crypto_regen', function (Blueprint $table) {            
            $table->dropForeign(['field_id']);
            $table->dropColumn(['field_id']);
        });
    }
}

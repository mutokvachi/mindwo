<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates master key groups table and add master key group to master key table
 */
class DxCryptoMasterkeyGroupsCreate extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_crypto_masterkey_groups', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('title', 250)->comment = trans('crypto.db.master_key_group_title');

            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });

        Schema::table('dx_crypto_masterkeys', function (Blueprint $table) {
            $table->integer('master_key_group_id')->unsigned()->comment = trans('crypto.db.master_key_group_title');

            $table->index('master_key_group_id');
            $table->foreign('master_key_group_id')->references('id')->on('dx_crypto_masterkey_groups')->onDelete('cascade');
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
            $table->dropForeign(['master_key_group_id']);
            $table->dropIndex(['master_key_group_id']);
            $table->dropColumn(['master_key_group_id']);
        });


        Schema::dropIfExists('dx_crypto_masterkey_groups');
    }
}

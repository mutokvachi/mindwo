<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxCryptoRegenCreate extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Schema::dropIfExists('dx_crypto_regen');

            // Creates new table for regeneration processes
            Schema::create('dx_crypto_regen', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');

                $table->text('master_key')->comment = trans('crypto.db.master_key');

                $table->integer('master_key_group_id')->unsigned()->comment = trans('crypto.db.master_key_group_title');

                $table->index('master_key_group_id');
                $table->foreign('master_key_group_id')->references('id')->on('dx_crypto_masterkey_groups')->onDelete('cascade');

                $table->integer('created_user_id')->nullable();
                $table->datetime('created_time')->nullable();
                $table->integer('modified_user_id')->nullable();
                $table->datetime('modified_time')->nullable();
            });

            \App\Models\Crypto\Cache::getQuery()->delete();

            // Removes unneded columns and adds new reference to regen table
            Schema::table('dx_crypto_cache', function (Blueprint $table) {
                $table->dropForeign(['master_key_group_id']);
                $table->dropIndex(['master_key_group_id']);
                $table->dropColumn(['master_key_group_id']);

                $table->integer('regen_id')->unsigned()->comment = trans('crypto.db.master_key_regen');
                $table->index('regen_id');
                $table->foreign('regen_id')->references('id')->on('dx_crypto_regen')->onDelete('cascade');
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
            Schema::table('dx_crypto_cache', function (Blueprint $table) {
                $table->integer('master_key_group_id')->unsigned()->comment = trans('crypto.db.master_key_group_title');
                $table->index('master_key_group_id');
                $table->foreign('master_key_group_id')->references('id')->on('dx_crypto_masterkey_groups')->onDelete('cascade');

                $table->dropForeign(['regen_id']);
                $table->dropIndex(['regen_id']);
                $table->dropColumn(['regen_id']);
            });

            Schema::dropIfExists('dx_crypto_regen');
        });
    }
}

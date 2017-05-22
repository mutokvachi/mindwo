<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxCryptoCacheCreate extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_crypto_cache', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');

            $table->string('ref_table', 50)->comment = trans('crypto.db.ref_table');
            $table->string('ref_column', 50)->comment = trans('crypto.db.ref_column');
            $table->integer('ref_id')->unsigned()->comment = trans('crypto.db.ref_id');
            $table->boolean('is_file')->comment = trans('crypto.db.is_file');
            $table->text('old_value')->nullable()->comment = trans('crypto.db.old_value');
            $table->text('new_value')->nullable()->comment = trans('crypto.db.new_value');

            $table->integer('master_key_group_id')->unsigned()->comment = trans('crypto.db.master_key_group_title');

            $table->index('master_key_group_id');
            $table->foreign('master_key_group_id')->references('id')->on('dx_crypto_masterkey_groups')->onDelete('cascade');

            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_crypto_cache');
    }
}
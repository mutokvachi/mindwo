<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Creates table for storing users' certificates
 */
class DxCryptoCertificatesCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::create('dx_crypto_certificates', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            
            $table->increments('id');
            $table->integer('user_id')->comment = trans('crypto.db.user_id');
            $table->binary('public_key')->comment = trans('crypto.db.public_key');
            $table->binary('private_key')->comment = trans('crypto.db.private_key');
            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users');
            
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
        Schema::dropIfExists('dx_crypto_certificates');
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDxAgreementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_agreements', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('role_id');
            $table->foreign('role_id')->references('role_id')->on('dx_users_roles')->onDelete('cascade');
            $table->text('agreement_text');
            $table->datetime('agreement_time');

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
        Schema::drop('dx_agreements');
    }
}

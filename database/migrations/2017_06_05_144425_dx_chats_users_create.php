<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxChatsUsersCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Schema::dropIfExists('dx_chats_users');

            // Creates new table for regeneration processes
            Schema::create('dx_chats_users', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');

                $table->integer('user_id')->comment = trans('form.chats.db.user');
                $table->integer('chat_id')->unsigned()->comment = trans('form.chats.chat');                

                $table->index('user_id');
                $table->foreign('user_id')->references('id')->on('dx_users')->onDelete('cascade');
                $table->index('chat_id');
                $table->foreign('chat_id')->references('id')->on('dx_chats')->onDelete('cascade');

                $table->integer('created_user_id')->nullable();
                $table->datetime('created_time')->nullable();
                $table->integer('modified_user_id')->nullable();
                $table->datetime('modified_time')->nullable();
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
            Schema::dropIfExists('dx_chats_users');
         });
    }
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxChatsMsgsCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Schema::dropIfExists('dx_chats_msgs');

            // Creates new table for regeneration processes
            Schema::create('dx_chats_msgs', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');

                $table->text('message')->comment = trans('form.chats.db.message');
                $table->string('file_name', 500)->nullable()->comment = trans('form.chats.db.file_name');
                $table->string('file_guid', 100)->nullable();

                $table->integer('chat_id')->unsigned()->comment = trans('form.chats.db.chat');

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
            Schema::dropIfExists('dx_chats_msgs');
         });
    }
}

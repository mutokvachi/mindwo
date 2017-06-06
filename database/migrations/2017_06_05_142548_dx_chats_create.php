<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxChatsCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            Schema::dropIfExists('dx_chats');

            // Creates new table for regeneration processes
            Schema::create('dx_chats', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');

                $table->integer('list_id')->comment = trans('form.chats.db.list');

                $table->index('list_id');
                $table->foreign('list_id')->references('id')->on('dx_lists')->onDelete('cascade');

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
            Schema::dropIfExists('dx_chats');
         });
    }
}

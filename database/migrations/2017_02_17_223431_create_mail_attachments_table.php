<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_mail_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mail_id')->unsigned();
            $table->string('file_name', 1000);
            $table->string('file_guid', 50);
            $table->integer('file_size')->unsigned();
            $table->string('mime_type', 50);
            $table->boolean('is_image');
            $table->foreign('mail_id')->references('id')->on('dx_mail');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dx_mail_attachments');
    }
}

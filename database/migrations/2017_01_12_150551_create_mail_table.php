<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dx_mail', function (Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->string('from');
			$table->string('to', 1000);
			$table->string('cc');
			$table->string('bcc');
			$table->string('subject', 1000);
			$table->text('body');
			$table->string('attachments', 1000);
			$table->boolean('is_read');
			$table->boolean('is_starred');
			$table->string('folder', 100);
			$table->datetime('sent_time');
			$table->datetime('received_time');
			
			$table->integer('created_user_id')->nullable();
			$table->datetime('created_time')->nullable();
			$table->integer('modified_user_id')->nullable();
			$table->datetime('modified_time')->nullable();
			
			$table->index('user_id');
			$table->foreign('user_id')->references('id')->on('dx_users')->onDelete('cascade');
			
			$table->index('folder');
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('dx_mail');
	}
}

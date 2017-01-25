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
			$table->string('folder', 100);
			$table->string('from');
			$table->string('to', 2000);
			$table->string('cc');
			$table->string('bcc');
			$table->string('subject', 2000);
			$table->text('body');
			$table->string('attachments', 1000);
			$table->boolean('is_read');
			$table->boolean('is_starred');
			
			$table->integer('created_user_id')->nullable();
			$table->datetime('created_time')->nullable();
			$table->integer('modified_user_id')->nullable();
			$table->datetime('modified_time')->nullable();
			$table->integer('sent_user_id')->nullable();
			$table->datetime('sent_time')->nullable();
			
			$table->index('folder');
			$table->index('to');
			$table->index('subject');
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

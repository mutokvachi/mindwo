<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSendTimeToMail extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('dx_mail', function (Blueprint $table)
		{
			$table->datetime('send_time')->nullable()->comment = 'Scheduled send time';
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('dx_mail', function (Blueprint $table)
		{
			$table->dropColumn('send_time');
		});
	}
}

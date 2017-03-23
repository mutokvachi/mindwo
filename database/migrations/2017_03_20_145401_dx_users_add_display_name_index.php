<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersAddDisplayNameIndex extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('dx_users', function (Blueprint $table)
		{
			$table->index('display_name', 'dx_users_display_name_index');
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('dx_users', function (Blueprint $table)
		{
			$table->dropIndex('dx_users_display_name_index');
		});
	}
}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersPositionsAddOrderIndex extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('dx_users_positions', function (Blueprint $table)
		{
			$table->integer('order_index')->default(0)->comment('Sorting criteria');
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('dx_users_positions', function (Blueprint $table)
		{
			$table->dropColumn('order_index');
		});
	}
}

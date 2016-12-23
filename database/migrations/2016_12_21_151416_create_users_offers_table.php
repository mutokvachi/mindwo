<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateUsersOffersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dx_users_offers', function (Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('offer_id');
			$table->integer('quantity');
			$table->datetime('created_time')->nullable();
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
		Schema::dropIfExists('dx_users_offers');
	}
}

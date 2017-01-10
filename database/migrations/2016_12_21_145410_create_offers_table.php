<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateOffersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dx_offers', function (Blueprint $table)
		{
			$table->increments('id');
			$table->string('code', 100)->comment = 'Code of an offer';
			$table->string('title', 1000)->comment = 'Title of an offer';
			$table->string('description', 1000)->comment = 'Title of an offer';
			$table->boolean('quantitative')->comment = 'An offer may have quantity specified';
			$table->date('valid_from');
			$table->date('valid_to');
			$table->integer('created_user_id')->nullable();
			$table->datetime('created_time')->nullable();
			$table->integer('modified_user_id')->nullable();
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
		Schema::dropIfExists('dx_offers');
	}
}

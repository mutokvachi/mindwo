<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUiThemesCreate extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dx_ui_themes', function (Blueprint $table)
		{
                        $table->engine = 'InnoDB';
			$table->increments('id');
			$table->string('title', 100);
			$table->string('file_name', 100);
			$table->string('file_guid', 100);
			$table->boolean('is_default', false);
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
		Schema::drop('dx_ui_themes');
	}
}

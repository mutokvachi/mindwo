<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersAddUiThemeId extends Migration
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
			$table->integer('ui_theme_id')->unsigned()->nullable()->comment = 'ID of a UI theme';
			$table->foreign('ui_theme_id')->references('id')->on('dx_ui_themes');
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
			$table->dropForeign(['ui_theme_id']);
			$table->dropColumn(['ui_theme_id']);
		});
	}
}

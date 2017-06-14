<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxFormsFieldsAddRowNumber extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('dx_forms_fields', function (Blueprint $table)
		{
			$table->integer('row_number')->unsigned()->nullable()->comment('Row number');
			$table->integer('col_number')->unsigned()->nullable()->comment('Column number');
		});
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('dx_forms_fields', function (Blueprint $table)
		{
			$table->dropColumn('row_number');
			$table->dropColumn('col_number');
		});
	}
}

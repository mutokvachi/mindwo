<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class DxTasksTypesSetCodes extends Migration
{
	protected $codes = [
		1 => 'APPR',
		2 => 'EXEC',
		3 => 'SUPP',
		4 => 'SET',
		5 => 'CRIT',
		6 => 'INFO',
		7 => 'CRITM',
		8 => 'CACT'
	];
	
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		foreach($this->codes as $id => $code)
		{
			DB::table('dx_tasks_types')
				->where('id', '=', $id)
				->update([ 'code' => $code ]);
		}
	}
	
	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('dx_tasks_types', function (Blueprint $table)
		{
			//
		});
	}
}

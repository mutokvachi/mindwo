<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class OrgChartController extends Controller
{
	public function show()
	{
		$result = view('organization.chart', [
			'self' => $this
		]);
		
		return $result;
	}
}

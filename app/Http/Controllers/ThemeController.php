<?php

namespace App\Http\Controllers;

use App\Models\UI\Theme;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;

class ThemeController extends Controller
{
	public function select($id)
	{
		$theme = Theme::find($id);
		
		if(!$theme)
		{
			return response([
				'success' => 0
			]);
		}
		
		$user = User::find(Auth::user()->id);
		
		$user->ui_theme_id = $id;
		$user->save();
		
		return response([
			'success' => 1
		]);
	}
}

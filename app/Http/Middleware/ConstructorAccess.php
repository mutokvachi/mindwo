<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * Class ConstructorAccess
 *
 * A middleware that restricts access to constructor interface to users which have access role specified in config
 * dx.constructor.access_role_id.
 *
 * @package App\Http\Middleware
 */
class ConstructorAccess
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$roleId = Config::get('dx.constructor.access_role_id');
		$user = User::find(Auth::user()->id);
		$hasRole = (boolean) $user->roles->where('id', $roleId)->count();
		
		if(!$hasRole)
		{
			return response(view('errors.attention', [
				'page_title' => trans('errors.access_denied_title'),
				'message' => trans('errors.no_rights_on_constructor')
			]));
		}
		
		return $next($request);
	}
}

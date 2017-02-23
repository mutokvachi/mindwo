<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

/**
 * Class MailAccess
 *
 * A middleware that restricts access to email interface to users which have access role specified in config
 * dx.email.access_role_id.
 *
 * @package App\Http\Middleware
 */
class MailAccess
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
		$roleId = Config::get('dx.email.access_role_id');
		$user = User::find(Auth::user()->id);
		$hasRole = (boolean) $user->roles->where('id', $roleId)->count();
		
		if(!$hasRole)
		{
			return response(view('errors.404'));
		}
		
		return $next($request);
	}
}

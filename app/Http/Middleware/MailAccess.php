<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class MailAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		$roleId = Config::get('dx.email_access_role_id');
		$user = User::find(Auth::user()->id);
		$hasRole = (boolean)$user->roles->where('id', $roleId)->count();
	
		if(!$hasRole)
			return response(view('errors.404'));
		
        return $next($request);
    }
}

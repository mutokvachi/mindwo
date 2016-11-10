<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Config;

class AjaxPublicAccess
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
        if (Auth::guest() && Config::get('dx.is_all_login_required', false)) {
            if ($request->ajax()) {
                return response()->json(['success' => 0, 'error' => trans('errors.session_ended')], 401);
            } else {
                return redirect()->guest('login');
            }
        }
        
        set_public_user($request);
        
        return $next($request);
    }
}

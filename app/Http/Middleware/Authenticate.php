<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Libraries\Helper;

class Authenticate
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
        if (!Auth::check()) {
            if ($request->ajax()) {
                 return response()->json(['success' => 0, 'error' => trans('errors.session_ended')], 401);
            } else {
                return redirect()->guest('login');
            }
        }
        
        set_default_view_params();
        set_cms_view_params();
        
        Helper::setBreadcrumbViewGlobals();
        
        return $next($request);
    }
}

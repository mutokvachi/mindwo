<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class ApiAccess
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
            return response()->json(['success' => 0, 'error' => trans('errors.access_denied_title')], 401);            
        }
        
        return $next($request);
    }
}

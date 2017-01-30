<?php

namespace App\Http\Middleware;

use Closure;
use App\Libraries\Helper;
use Log;
    
class FilePublicAccess
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
        Log::info("MIDLE: " . $request->url() . " method: " . $request->method());
        return $next($request);
    }
}
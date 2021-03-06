<?php

namespace App\Http\Middleware;

use Closure;
use App\Libraries\Helper;

class PublicAccess
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
        if (!Helper::isUserPublicAccessOk()) {
            return redirect()->guest('login');
        }
        
        set_public_user($request);

        set_default_view_params();
        set_cms_view_params();
        
        Helper::setBreadcrumbViewGlobals();
        
        return $next($request);
    }

}

<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Config;
use App\Http\Controllers\BoxController;
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
        if (Auth::guest() && Config::get('dx.is_all_login_required', false)) {
            return redirect()->guest('login');
        }
        
        set_public_user($request);

        set_default_view_params();
        set_cms_view_params();
        
        $sliderMenu = BoxController::generateSlideMenu();
        view()->share('slidable_htm', $sliderMenu);    
        view()->share('breadcrumb', Helper::getBreadcrumb($request->url()));
        view()->share('is_slidable_menu', Config::get('dx.is_slidable_menu'));
        
        return $next($request);
    }

}

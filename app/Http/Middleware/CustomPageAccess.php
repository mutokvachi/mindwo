<?php

namespace App\Http\Middleware;

use DB;
use Closure;
use App\Exceptions;
use App\Libraries\Helper;
use Auth;

/**
 * Class CustomPageAccess
 *
 * A middleware that restricts access to custom pages (not made via CMS) like mail, orgchart etc built in pages 
 *
 * @package App\Http\Middleware
 */
class CustomPageAccess
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
        if (!Auth::check()) {
            if ($request->ajax()) {
                 return response()->json(['success' => 0, 'error' => trans('errors.session_ended')], 401);
            } else {
                return redirect()->guest('login');
            }
        }

        $request->root();
        $url = str_replace($request->root() . "/", "", $request->url());

        $arr_url = explode("/", $url);
        $page = $this->getPage($arr_url, $arr_url[0], 0);

        if (!$page) {
            throw new Exceptions\DXCustomException(trans('errors.custom_page_not_found', ['url' => $url]));
        }

        if (!\mindwo\pages\Rights::getRightsOnPage($page->id)) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_custom_page', ['page' => $page->title]));
        } 
        
        set_default_view_params();
        set_cms_view_params();
        
        Helper::setBreadcrumbViewGlobals();
		
		return $next($request);
    }
    
    /**
     * Recursively try to get page row by request URL
     * For example url calendar/complect/0 will be checked first by "calendar" then by "calendar/complect" etc
     *
     * @param array $arr_url URL exploed by /
     * @param string $url First part of URL - added next parts on each iteration
     * @param string $pos Current position in array
     * @return array Page row from table dx_pages
     */
    private function getPage($arr_url, $url, $pos) {
        $page = DB::table('dx_pages')
                ->where('url_title', '=', $url)
                ->where('is_active', '=', 1)
                ->first();
        
        if ($page) {
            return $page;            
        }

        if ($pos == (count($arr_url)-1)) {
            return null;
        }

        $pos++;
        $url .= "/" . $arr_url[$pos];
        return $this->getPage($arr_url, $url, $pos);
    }
}

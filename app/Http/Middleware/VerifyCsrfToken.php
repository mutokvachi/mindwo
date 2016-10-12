<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Illuminate\Session\TokenMismatchException;
use Auth;
use Config;
use Closure;
use \App\Exceptions;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'block_ajax', 'img/*'
    ];
    
    /**
    * Kontrolē CSRF token derīgumu - kontrole tiek veikta tikai autorizētiem lietotājiem
    *
    * @param   Request     $request    GET/POST pieprasījuma objekts
    * @param   Closure     $next       Nākamā pieprasījuma objekts, ja token ir derīgs
    * @return  mixed
    */
    public function handle($request, Closure $next)
    {          
        if (!$this->isReading($request) && Auth::check())
        {
            $public_user_id = Config::get('dx.public_user_id');
            if (!$public_user_id)
            {
                throw new Exceptions\DXCustomException("Resursam '" . $request->url() . "' nav iespējams piekļūt, jo sistēmas iestatījumos nav norādīts publiskais lietotājs!");
            }
            
            if (Auth::user()->id != $public_user_id && !$this->tokensMatch($request))
            {
                // If user try to re login then allow continue
                if ($request->path() === "relogin")
                {
                    return $next($request);
                }
                
                //throw new TokenMismatchException;
                return response()->json(['success' => 0, 'error' => 'Lietotāja sesija ir beigusies!'], 401);
            }
        }
        
        return $next($request);
    }
}

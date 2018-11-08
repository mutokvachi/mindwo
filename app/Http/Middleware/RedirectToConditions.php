<?php

namespace App\Http\Middleware;

use DB;
use Config;
use Closure;
use Session;

class RedirectToConditions
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
        $envChecker = Config::get('dx.TERMS_CONDITIONS');

        if($envChecker !== true)
            return $next($request);

        $user_id = Session::get('my_user_id');
        $role_id = Session::get('my_user_role');

        $condition = DB::table('dx_conditions')->where('user_id',$user_id)->first();
        $terms = DB::table('dx_condition_roles')->where('role_id',$role_id)->first();

        if(!isset($terms)){
            dd('There are no terms&conditions text for this role!');
        }

        if(empty($condition) || json_decode($terms->time) > json_decode($condition->time)){
            return redirect('/conditions');
        }

        if(isset($condition->status)){
            if($condition->status != 'agree'){
                return redirect('/conditions');
            }
        }

        return $next($request);
    }
}

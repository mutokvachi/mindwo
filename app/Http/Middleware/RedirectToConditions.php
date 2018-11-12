<?php

namespace App\Http\Middleware;

use DB;
use Auth;
use Config;
use Closure;
use Session;
use App\Models\Agreement;
use App\Models\AgreementAudit;

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


        if ($request->ajax()) {
            return $next($request); // we ignore AJAX requests
        }

        if (!Auth::check() || Auth::user()->id == config('dx.public_user_id')) {
            return $next($request); // we ignore non-authenicated users or public user who dont need login
        }

        if($request->segment(1) == 'conditions' || $request->segment(1) == 'conditionsStatus'){
            return $next($request);
        }

        $envChecker = Config::get('dx.TERMS_CONDITIONS');

        if($envChecker !== true)
            return $next($request);
        

        $user_id = Auth::user()->id;
        $roles = DB::table('dx_users_roles')->where('user_id',$user_id)->pluck('role_id');

        $agreed = AgreementAudit::where('user_id', $user_id)->get()->keyBy('agreement_id');
        $agreements = Agreement::whereIn('role_id', $roles)->get()->keyBy('role_id');


        foreach($roles as $key => $role){
            if(!isset($agreed[$role])){
                $terms = DB::table('dx_agreements')->where('role_id', $role)->first();
                return redirect()->route('conditions', ['id'=>$terms->id]);
            }elseif(isset($agreements[$role]->agreement_time)){
                if($agreements[$role]->agreement_time > $agreed[$role]->accepted_time){
                    $terms = DB::table('dx_agreements')->where('role_id', $role)->first();
                    return redirect()->route('conditions', ['id'=>$terms->id]);
                }
            }
        }


        return $next($request);
    }
}

<?php

namespace App\Http\Controllers;

use DB;
use Log;
use Hash;
use Auth;
use Config;
use Session;
use App\Http\Requests;
use Illuminate\Http\Request;

class ConditionController extends Controller
{

	public function conditions($id){
        $user_id = Auth::user()->id;
        $roles = DB::table('dx_users_roles')->where('user_id',$user_id)->pluck('role_id');

        $terms = DB::table('dx_agreements')->where('id', $id)->first();

        if(empty($terms)){
            dd('No terms exists for this role');
        }

        $agreed = DB::table('dx_agreement_audits')
            ->where('agreement_id',$terms->role_id)
            ->where('user_id', $user_id)
            ->first();

        if(isset($agreed) && $agreed->accepted_time > $terms->agreement_time){
            return redirect('/');
        }


        return view('conditions', compact('terms'));
    }

    public function conditionsStatus($agreement_id){
        $time = date('Y-m-d H:m:s', time());

        $user_id = Auth::user()->id;

        if($agreement_id == 'decline'){
			Auth::logout();
	        return redirect()->route('login');
        }
        
        if(!isset($user_id)){
            Auth::logout();
	        return redirect()->route('login');
        }
        
        DB::table('dx_agreement_audits')
            ->where('user_id', $user_id)
            ->where('agreement_id', $agreement_id)
            ->delete();

        DB::table('dx_agreement_audits')->insert([
            'user_id'       => $user_id,
            'agreement_id'  => $agreement_id,
            'accepted_time' => $time
        ]);

        return redirect('/');
    }

    public function test(){


        dd('All terms&conditions are already agreed !');
    }
    
}

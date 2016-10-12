<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Exceptions;
use Auth;
use DB;
use Config;
use Adldap\Adldap;
use Hash;
use Log;

class UserController extends Controller
{
    /**
      *
      * Lietotāju autorizācijas kontrolieris
      * Objekts nodrošina lietotāju autorizāciju - primāri izmantojot Active Directory autorizācijas mehānismu
      *
     */
    
    private $try_limit = 3;
    private $temp_block_minutes = 2;
    
    /**
     * Indicates if user will be blocked on wrong password
     * 
     * @var boolean 
     */
    private $is_user_to_block = 0;
    
    /**
     * Autorizē lietotāju izmantojot Active Directory (LDAP) vai arī portāla lietotāju autorizācijas mehānismu (Laravel)
     * 
     * @param string $user_name      Lietotāja vārds
     * @param string $user_passw     Lietotāja parole
     * @return Response Veiksmīgas autorizācijas gadījumā noklusētā lapa, neveiksmīgas autorizācijas gadījumā kļūdas paziņojums
     */

    public function loginUser(Request $request)
    {
        $this->validate($request, [
            'user_name' => 'required|min:3',
            'password' => 'required|min:8'
        ]);
        
        $user_name = $request->input('user_name');
        $pass = $request->input('password');
        
        $this->try_limit = Config::get('auth.try_count');
        $this->temp_block_minutes = Config::get('auth.temp_block_minutes');
        
        try
        {
            $this->authoriseUser($user_name, $pass);

            if (Auth::user()->is_blocked)
            {
                Auth::logout();
                throw new Exceptions\DXCustomException(trans('errors.user_is_blocked'));
            }
            
            $this->updateAttempts(Auth::user()->id , 0);
            
            DB::table('in_portal_log')->insert([['log_time' => date('Y-n-d H:i'), 'url' => 'login', 'user_id' => Auth::user()->id]]);
            
            $dx_redirect = $request->session()->pull('dx_redirect', '');
            
            if (strlen($dx_redirect) > 0){
                return redirect()->away($dx_redirect);
            }else {            
                return redirect()->intended('/');
            }
        }        
        catch (\Exception $e)
        {
            return view('index', [
                'error' => $e->getMessage()
            ]);
        }
    }
    
    public function reLoginUser(Request $request)
    {
        $result = array('success' => 0);
        
        $this->validate($request, [
            'user_name' => 'required|min:3',
            'password' => 'required|min:8'
        ]);
                
        $user_name = $request->input('user_name');
        $pass = $request->input('password');
        
        $this->try_limit = Config::get('auth.try_count');
        $this->temp_block_minutes = Config::get('auth.temp_block_minutes');
        
        try
        {
            $this->authoriseUser($user_name, $pass);

            if (Auth::user()->is_blocked)
            {
                Auth::logout();
                throw new Exceptions\DXCustomException(trans('errors.user_is_blocked'));
            }
            
            $this->updateAttempts(Auth::user()->id , 0);
            
            DB::table('in_portal_log')->insert([['log_time' => date('Y-n-d H:i'), 'url' => 'login', 'user_id' => Auth::user()->id]]);
            $token = csrf_token();
            
            $result['success'] = 1;
            $result['token'] = $token;
        }        
        catch (\Exception $e)
        {
            $result['error'] = $e->getMessage();
        }
        
        return response()->json($result);
    }

    /**
     * Pārtrauc autorizētā lietotāja sesiju
     *
     * @return Response Pārvirza uz sākuma lapu
     */

    public function logOut()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    /**
     * Nodrošina autorizācijas lapas atvēršanu GET pieprasījumam
     *
     * @return Response Autorizācijas lapa
     */

    public function showIndex(Request $request)
    {
        $is_blocked = $request->cookie('is_blocked');
        
        if ($is_blocked != 1)
        {
            $is_blocked = 0;
        }
        
        return view('index', ['error' => '']);
    }
    
    /**
     * Lietotāja paroles nomaiņa
     * 
     * @param \Illuminate\Http\Request $request AJAX POST pieprasījuma objekts
     * @return Response JSON rezultāts
     * @throws Exceptions\DXCustomException
     */
    public function changePassw(Request $request)
    {
        $this->validate($request, [
            'pass_old' => 'required|min:8',
            'pass_new1' => 'required|min:8',
            'pass_new2' => 'required|min:8|same:pass_new1'
        ]);
        
        $pass_old = $request->input('pass_old');
        $pass_new1 = $request->input('pass_new1');
        
        if (!Auth::attempt(['login_name' => Auth::user()->login_name, 'password' => $pass_old])){
            throw new Exceptions\DXCustomException(trans('errors.wrong_current_password'));
        }
        
        DB::table('dx_users')->where('id', '=', Auth::user()->id)->update(['password' => Hash::make($pass_new1)]);
                
        return response()->json(['success' => 1]);
    }
    
    public function formPassw(Request $request) {
        
        $htm = view('main.password_form')->render();
        return response()->json(['success' => 1, 'html' => $htm]); 
    }

    /**
     * Autorizē lietotāju. Vispirms mēģina ar Active Directory. Ja neizdodas, tad ar SVS lietotāju
     * 
     * @param string $user_name Lietotāja vārds
     * @param string $pass Lietotāja parole
     * @return void
     * @throws Exceptions\DXCustomException
     */
    private function authoriseUser($user_name, $pass)
    {
        if ($this->authorizeLDAP($user_name, $pass))
        {
            return; // veiksmīga autorizācija ar Active Directory
        }
            
        $user_row = $this->getUserByLogin('login_name', $user_name);
        $this->checkAttempts($user_row);

        if (!Auth::attempt(['login_name' => $user_name, 'password' => $pass]))
        { 
            $this->updateAttempts($user_row->id , $user_row->auth_attempts + 1);
            
            if ($this->is_user_to_block) {
               $this->blockUser($user_row);             
            }

            throw new Exceptions\DXCustomException(trans('errors.wrong_user_or_password'));
        }            
    }
    
    /**
     * Blocks user
     * 
     * @param object $user_row User data row
     * @throws Exceptions\DXCustomException
     */
    private function blockUser($user_row) {
        DB::table('dx_users')
        ->where('id', $user_row->id)
        ->update(['last_attempt' => date("Y-m-d H:i:s"), 'auth_attempts' => $user_row->auth_attempts + 1, 'is_blocked' => 1]);

        throw new Exceptions\DXCustomException(sprintf(trans('errors.login_attempts_exceeded'), ($this->try_limit + 1)));
    }
    
    /**
     * Funkcija autorizē lietotāju izmantojot Active Directory (LDAP)
     * Autorizācijas komponente: https://github.com/adldap/adLDAP
     * 
     * @param string $user_name      Lietotāja vārds (bez domēna)
     * @param string $user_passw     Lietotāja parole
     * @return bool Ja LDAP autorizācija sekmīga, tad true, pretējā gadījumā false
     */

    private function authorizeLDAP($user_name, $user_passw)
    {
        if (!Config::get('ldap.use_ldap_auth'))
        {
            return false;
        }
        
        $user_row = $this->getUserByLogin('ad_login', $user_name);
        
        $this->checkAttempts($user_row);
        
        $config = array(
            'account_suffix' => Config::get('ldap.account_suffix'),
            'domain_controllers' => array(Config::get('ldap.domain_controller')),
            'base_dn' => Config::get('ldap.base_dn'),
            'admin_username' => Config::get('ldap.admin_username'),
            'admin_password' => Config::get('ldap.admin_password'),
        );

        $ad = new Adldap($config);

        if ($ad->authenticate($user_name, $user_passw))
        {
            Auth::loginUsingId($user_row->id);            
            return true;
        }
        
        if ($this->is_user_to_block) {
            $this->blockUser($user_row);
        }
        
        $this->updateAttempts($user_row->id , $user_row->auth_attempts + 1);
        throw new Exceptions\DXCustomException(trans('errors.wrong_user_or_password'));
        
    }
    
    /**
     * Pārbauda autorizācijas mēģinājumu skaitu
     * Nepieciešamības gadījumā bloķē lietotāju
     * 
     * @param Object $user_row  Lietotāja ieraksta rinda
     * @throws Exceptions\DXCustomException
     * @throws Exceptions\DXBlockException
     */
    private function checkAttempts($user_row)
    { 
        if ($user_row->is_blocked)
        {
            throw new Exceptions\DXCustomException(trans('errors.user_is_blocked'));
        }
        
        if ($user_row->auth_attempts == $this->try_limit)
        {
            $this->updateAttempts($user_row->id , $user_row->auth_attempts + 1);
            
            throw new Exceptions\DXCustomException(sprintf(trans('errors.login_attempts_warning_minutes'), $this->try_limit, $this->temp_block_minutes));
        }
        
        if ($user_row->auth_attempts > $this->try_limit)
        {
            $timeFirst  = strtotime($user_row->last_attempt);
            $timeSecond = strtotime(date("Y-m-d H:i:s"));
            $interval = $timeSecond - $timeFirst;
            
            if ($interval > $this->temp_block_minutes*60)
            {
                $this->is_user_to_block = 1;
            }
            else
            {
                $interval = $this->temp_block_minutes*60 - $interval;
                throw new Exceptions\DXCustomException(sprintf(trans('errors.login_attempts_warning_seconds'), $this->try_limit, $interval));
            }
        }
    }
    
    /**
     * Izgūst lietotāja rindas objektu pēc norādītā lietotāja vārda
     * 
     * @param string $login_field   Lietotāja vārda lauka nosaukums
     * @param string $login_name    Lietotāja vārds
     * @return Object Lietotāja rindas objekts
     * @throws Exceptions\DXCustomException
     */
    private function getUserByLogin($login_field, $login_name)
    {
        $user_row = DB::table('dx_users')->where($login_field, '=', $login_name)->first();

        if (!$user_row)
        {
            throw new Exceptions\DXCustomException(trans('errors.wrong_user_or_password'));
        }
        
        return $user_row;
    }
    
    /**
     * Uzstāda autorizācijas mēģinājumu skaitītāju
     * 
     * @param integer $user_id  Lietotāja ID
     * @param type $attempt     Autorizācijas mēģinājumu skaits
     */
    private function updateAttempts($user_id, $attempt)
    {
        DB::table('dx_users')
            ->where('id', $user_id)
            ->update(['last_attempt' => date("Y-m-d H:i:s"), 'auth_attempts' => $attempt]);
    }

}

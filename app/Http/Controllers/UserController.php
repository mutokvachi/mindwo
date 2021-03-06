<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exceptions;
use Auth;
use DB;
use Config;
use Hash;
use Log;
use Session;
/**
 * User authorization controller. 
 * It can use different authentication methods - OpenLDAP, Active Directory, default systems authentication (Laravel)
 */
class UserController extends Controller
{
    /**
     * Possible authentication types
     * @var array 
     */
    private $auth_types = array('DEFAULT', 'AD', 'OPENLDAP');

    /**
     * Allowed try limit for authorization 
     * @var integer
     */
    private $try_limit = 3;

    /**
     * Minutes how long authorization is blocked after try limit has been reached
     * @var integer 
     */
    private $temp_block_minutes = 2;

    /**
     * Indicates if user will be blocked on wrong password
     * 
     * @var boolean 
     */
    private $is_user_to_block = 0;

    /**
     * If Active Directory or OpenLDAP authentication succeed but user doesn't exist then if this option is true, new user will be created
     * @var boolean 
     */
    private $create_user_if_not_exist = true;

    /**
     * Initiate controller 
     */
    public function __construct()
    {
        $this->create_user_if_not_exist = Config::get('auth.create_user_if_not_exist');
    }

    /**
     * Attempts to authorize user into system
     * 
     * @param \Illuminate\Http\Request $request Request object
     * @return Response If succes then opens default page, else error page will be opened
     */
    public function loginUser(Request $request)
    {
        $this->validate($request, [
            'user_name' => 'required',
            'password' => 'required'
        ]);

        $user_name = $request->input('user_name');
        $pass = $request->input('password');

        $this->try_limit = Config::get('auth.try_count');
        $this->temp_block_minutes = Config::get('auth.temp_block_minutes');

        try {
            $this->authenticateUser($request, $user_name, $pass);
            
            if (!Auth::user()) {
                throw new Exceptions\DXCustomException(trans('errors.wrong_user_or_password'));
            }

            if (Auth::user()->is_blocked) {
                Auth::logout();
                throw new Exceptions\DXCustomException(trans('errors.user_is_blocked'));
            }

            $this->updateAttempts(Auth::user()->id, 0);

            DB::table('in_portal_log')->insert([['log_time' => date('Y-n-d H:i'), 'url' => 'login', 'user_id' => Auth::user()->id]]);
                        
            // add default roles if not added jet
            $this->addDefaultRoles();
            
            $dx_redirect = $request->session()->pull('dx_redirect', '');

            if (strlen($dx_redirect) > 0) {
                return redirect()->away($dx_redirect);
            } else {
                return redirect()->intended('/');
            }
        } catch (\Exception $e) {
            return view('index', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Repeatedly attempts to authorize user into system
     * @param Request $request Request's data
     * @return Response JSON response
     */
    public function reLoginUser(Request $request)
    {
        $result = array('success' => 0);

        $this->validate($request, [
            'user_name' => 'required',
            'password' => 'required'
        ]);

        $user_name = $request->input('user_name');
        $pass = $request->input('password');

        $this->try_limit = Config::get('auth.try_count');
        $this->temp_block_minutes = Config::get('auth.temp_block_minutes');

        try {
            $this->authenticateUser($request, $user_name, $pass);

            if (!Auth::user()) {
                throw new Exceptions\DXCustomException(trans('errors.wrong_user_or_password'));
            }

            if (Auth::user()->is_blocked) {
                Auth::logout();
                throw new Exceptions\DXCustomException(trans('errors.user_is_blocked'));
            }

            $this->updateAttempts(Auth::user()->id, 0);

            DB::table('in_portal_log')->insert([['log_time' => date('Y-n-d H:i'), 'url' => 'login', 'user_id' => Auth::user()->id]]);
            $token = csrf_token();

            $result['success'] = 1;
            $result['token'] = $token;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return response()->json($result);
    }

    /**
     * Destroyes user's session
     *
     * @return Response Redirect to login page
     */
    public function logOut()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    /**
     * Provides authorization page for "GET" request
     *
     * @return object Authorization page view
     */
    public function showIndex(Request $request)
    {
        $is_blocked = $request->cookie('is_blocked');

        if ($is_blocked != 1) {
            $is_blocked = 0;
        }

        return view('index', ['error' => '']);
    }

    /**
     * Users password change
     * 
     * @param \Illuminate\Http\Request $request AJAX POST request object
     * @return Response JSON rezult
     * @throws Exceptions\DXCustomException Error message
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

        if (!Auth::attempt(['login_name' => Auth::user()->login_name, 'password' => $pass_old])) {
            throw new Exceptions\DXCustomException(trans('errors.wrong_current_password'));
        }

        DB::table('dx_users')->where('id', '=', Auth::user()->id)->update(['password' => Hash::make($pass_new1)]);

        return response()->json(['success' => 1]);
    }

    /**
     * Shows password form for password change
     * @param Request $request
     * @return object JSON which contains password form for changing password
     */
    public function formPassw(Request $request)
    {
        $htm = view('main.password_form')->render();
        return response()->json(['success' => 1, 'html' => $htm]);
    }
    
    /**
     * Updates user roles - add automaticaly default roles to user
     */
    private function addDefaultRoles() {
       
	$def_roles = DB::table('dx_roles as r')
                     ->select('r.id')
                     ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                              ->from('dx_users_roles as ur')
                              ->where('ur.user_id', '=', Auth::user()->id)
                              ->whereRaw('ur.role_id = r.id');
                     })
                     ->where('r.is_default', '=', 1)
                     ->get();
        
	DB::transaction(function () use ($def_roles) {	
            foreach($def_roles as $role) {
                DB::table('dx_users_roles')->insert([
                    'user_id' => Auth::user()->id,
                    'role_id' => $role->id
                ]);
            }
        });        
    }

    /**
     * Authenticates user in system.
     * 
     * @param \Illuminate\Http\Request $request Request object
     * @param string $user_name Users login name
     * @param string $password Users password
     * @return void
     * @throws Exceptions\DXCustomException Error message
     */
    private function authenticateUser(Request $request, $user_name, $password)
    {
        // Retrieve used authentication types from configuration
        $auth_types = explode(';', Config::get('auth.type'));

        // Checks if any auth method is specified
        if ($auth_types < 1) {
            throw new Exceptions\DXCustomException(trans('errors.missing_auth_method'));
        }

        $auth_succ = false;

        // Iterates through authentication methods
        foreach ($auth_types as $auth_type) {
            $type = strtoupper($auth_type);

            try {
                // Check if specified authentication type exists
                if (in_array($type, $this->auth_types)) {
                    $function_name = 'authenticate' . $type;

                    // Execute authentication process
                    $auth_succ = $this->{$function_name}($request, $user_name, $password);
                }
            } catch (\Exception $e) {
                Log::info("ERROR: " . $e->getMessage());
            }

            // If user is authenticated then quites loop
            if ($auth_succ) {
                break;
            }
        }

        if (!$auth_succ) {
            throw new Exceptions\DXCustomException(trans('errors.wrong_user_or_password'));
        }
    }

    /**
     * If user not authenticated then save failed attempt
     * @param array $user_row user data row
     * @param boolean $is_auth_success Parameter if authentication succeeded
     */
    private function registerFailedAuth($user_row, $is_auth_success)
    {
        if (!$is_auth_success) {
            $this->updateAttempts($user_row->id, $user_row->auth_attempts + 1);

            if ($this->is_user_to_block) {
                $this->blockUser($user_row);
            }
        }
    }

    /**
     * Authenticate user useing Active Directory
     * @param \Illuminate\Http\Request $request Request object
     * @param type $user_name users login name
     * @param type $user_password User password
     * @return boolean Result if authentication succeeded
     */
    private function authenticateAD(Request $request, $user_name, $user_password)
    {
        $user_row = $this->getUserByLogin('email', $user_name);
        $this->checkAttempts($user_row);

        $ad = new \App\Libraries\Auth\ActiveDirectory();
        $is_auth_success = $ad->auth($user_row, $user_name, $user_password);

        $this->registerFailedAuth($user_row, $is_auth_success);

        return $is_auth_success;
    }

    /**
     * Authenticate user using OpenLDAP
     * @param \Illuminate\Http\Request $request Request object
     * @param type $user_name users login name
     * @param type $user_password User password
     * @return boolean Result if authentication succeeded
     */
    private function authenticateOPENLDAP(Request $request, $user_name, $user_password)
    {
        $user_row = $this->getUserByLogin('email', $user_name);

        $this->checkAttempts($user_row);

        $ad = new \App\Libraries\Auth\OpenLDAP();
        $is_auth_success = $ad->auth($user_row, $user_name, $user_password);

        $this->registerFailedAuth($user_row, $is_auth_success);

        return $is_auth_success;
    }

    /**
     * Authenticate user using default authroization system
     * @param \Illuminate\Http\Request $request Request object
     * @param type $user_name users login name
     * @param type $password User password
     * @return boolean Result if authentication succeeded
     */
    private function authenticateDEFAULT(Request $request, $user_name, $password)
    {
        $user_row = $this->getUserByLogin('login_name', $user_name);
        $this->checkAttempts($user_row);

        $is_auth_success = Auth::attempt(['login_name' => $user_name, 'password' => $password]);

        $this->registerFailedAuth($user_row, $is_auth_success);

        return $is_auth_success;
    }

    /**
     * Blocks user
     * 
     * @param object $user_row User data row
     * @throws Exceptions\DXCustomException
     */
    private function blockUser($user_row)
    {
        DB::table('dx_users')
                ->where('id', $user_row->id)
                ->update(['last_attempt' => date("Y-m-d H:i:s"), 'auth_attempts' => $user_row->auth_attempts + 1, 'is_blocked' => 1]);

        throw new Exceptions\DXCustomException(sprintf(trans('errors.login_attempts_exceeded'), ($this->try_limit + 1)));
    }

    /**
     * Check authentication attempts and if user is blocked
     * 
     * @param Object $user_row  User's data row
     * @throws Exceptions\DXCustomException
     * @throws Exceptions\DXBlockException
     */
    private function checkAttempts($user_row)
    {
        if (!$user_row) {
            return;
        }

        if ($user_row->is_blocked) {
            throw new Exceptions\DXCustomException(trans('errors.user_is_blocked'));
        }

        if ($user_row->auth_attempts == $this->try_limit) {
            $this->updateAttempts($user_row->id, $user_row->auth_attempts + 1);

            throw new Exceptions\DXCustomException(sprintf(trans('errors.login_attempts_warning_minutes'), $this->try_limit, $this->temp_block_minutes));
        }

        if ($user_row->auth_attempts > $this->try_limit) {
            $timeFirst = strtotime($user_row->last_attempt);
            $timeSecond = strtotime(date("Y-m-d H:i:s"));
            $interval = $timeSecond - $timeFirst;

            if ($interval > $this->temp_block_minutes * 60) {
                $this->is_user_to_block = 1;
            } else {
                $interval = $this->temp_block_minutes * 60 - $interval;
                throw new Exceptions\DXCustomException(sprintf(trans('errors.login_attempts_warning_seconds'), $this->try_limit, $interval));
            }
        }
    }

    /**
     * Gets user by login
     * 
     * @param string $login_field User's login name field name in data base
     * @param string $login_name User's login name
     * @return Object User's data row
     * @throws Exceptions\DXCustomException
     */
    private function getUserByLogin($login_field, $login_name)
    {
        $user_row = \App\User::where($login_field, '=', $login_name)->first();

        if (!$user_row && !$this->create_user_if_not_exist) {
            throw new Exceptions\DXCustomException(trans('errors.wrong_user_or_password'));
        }

        return $user_row;
    }

    /**
     * Updates authentication attempts for user
     * 
     * @param integer $user_id User's ID
     * @param type $attempt Attempt count
     */
    private function updateAttempts($user_id, $attempt)
    {
        DB::table('dx_users')
                ->where('id', $user_id)
                ->update(['last_attempt' => date("Y-m-d H:i:s"), 'auth_attempts' => $attempt]);
    }

    

}
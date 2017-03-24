<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;
use Log;
use Config;
use Carbon\Carbon;

/**
 * Model for systems users
 */
class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
	use Authenticatable,
		CanResetPassword;
	/**
	 * Changes default column name for column updated_at
	 */
	const UPDATED_AT = 'modified_time';
	/**
	 * Changes default column name for column created_at
	 */
	const CREATED_AT = 'created_time';
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'dx_users';
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'login_name',
		'display_name',
		'position_title',
		'picture_guid',
		'email',
		'password',
		'id',
		'is_blocked',
		'updated_at',
		'doc_country_id'
	];
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];
	/**
	 * Here we cache array of lists the user has access to.
	 *
	 * @var
	 */
	protected $lists;
        
        /**
         * Array with leave info (if employee is in vacation or sick or his country have holiday)
         * @var array
         */
        private $leave_info = [];
        
        /**
         * Get time after joining in textual format
         * @return string Returns for examle 1 year 2 months and 3 days
         */
        public function getJoinTime() {
            
            if (!$this->join_date) {
                return "";
            }
            
            if (!$this->termination_date) {
                $now = Carbon::now(Config::get('dx.time_zone'));
            }
            else {
                $now = Carbon::createFromFormat("Y-m-d", $this->termination_date);
            }
            
            $join = Carbon::createFromFormat("Y-m-d", $this->join_date);
            
            $y = $join->diffInYears($now);
                        
            $y_full = $join->addYears($y);
            $m = $y_full->diffInMonths($now);
            
            $m_full = $y_full->addMonth($m);
            
            $d = $m_full->diffInDays($now);
            
            return ($y > 0 ? $y . " " . ($y > 1 ? trans('employee.lbl_year_n') : trans('employee.lbl_year_1')) . " " : "") . 
                   ($m > 0 ? $m . " " . ($m > 1 ? trans('employee.lbl_month_n') : trans('employee.lbl_month_1')) . " " : "") . 
                   ($d > 0 ? $d . " " . ($d > 1 ? trans('employee.lbl_day_n') : trans('employee.lbl_day_1')) : "");
        }
        
        /**
         * Checks and returns employee leave info array
         * @return array
         */
        public function getLeaveInfo() {
            if ($this->leave_info) {
                return $this->leave_info;
            }
            
            if (!$this->left_to || (!$this->left_holiday_id && !$this->left_reason_id)) {
                return [];
            }
            
            $this->leave_info['left_to'] = short_date($this->left_to);  
            
            if (!$this->left_reason_id) {
                $this->leave_info['reason_title'] = trans('employee.lbl_holiday');
                
                $hol = DB::table('dx_holidays as h')
                        ->select('h.title as holiday_title', 'c.title as country_title')
                        ->leftJoin('dx_countries as c', 'h.country_id', '=', 'c.id')
                        ->where('h.id', '=', $this->left_holiday_id)
                        ->first();
                
                $this->leave_info['reason_details'] = (($hol->country_title) ? $hol->country_title . " - " : "") . $hol->holiday_title;
            }
            else {
                $this->leave_info['reason_title'] = DB::table('dx_timeoff_types')->select('title')->where('id', '=', $this->left_reason_id)->first()->title;
                $this->leave_info['reason_details'] = '';
            }
            
            if (!$this->substit_empl_id) {
                $this->leave_info['substitute_name'] = '';
                $this->leave_info['substitute_id'] = 0;
            }
            else {
                $this->leave_info['substitute_name'] = DB::table('dx_users')->select('display_name')->where('id', '=', $this->substit_empl_id)->first()->display_name;
                $this->leave_info['substitute_id'] = $this->substit_empl_id;
            }
            
            return $this->leave_info;
        }
        
	/**
	 * Relation to user roles
	 *
	 * @return App\Models\System\Role
	 */
	public function roles()
	{
		return $this->belongsToMany('App\Models\System\Role', 'dx_users_roles', 'user_id', 'role_id');
	}
	
	/**
	 * Relation to user access
	 *
	 * @return App\Models\System\Role
	 */
	public function access()
	{
		return $this->belongsToMany('App\Models\System\Role', 'dx_users_access', 'user_id', 'role_id');
	}
	
	/**
	 * List of users's personal documents
	 * @return App\Models\Employee\EmployeePersonalDocument
	 */
	public function employeePersonalDocs()
	{
		return $this->hasMany('\App\Models\Employee\EmployeePersonalDocument', 'user_id');
	}
        
        /**
	 * List of users's notes
	 * @return App\Models\Employee\Note
	 */
	public function notes()
	{
		return $this->hasMany('\App\Models\Employee\Note', 'user_id');
	}
        
        /**
	 * List of users's time off calculations
	 * @return App\Models\Employee\TimeoffCalc
	 */
	public function timeoffCalc()
	{
		return $this->hasMany('\App\Models\Employee\TimeoffCalc', 'user_id');
	}
        
        /**
	 * Users's time off data
	 * @return object Contains time off types data and value for specific user
	 */
	public function timeoff()
	{
            $timeoffs =  DB::table('dx_timeoff_types as to')
                         ->where('to.is_disabled', '=', 0)
                         ->orderBy('to.order_index')
                         ->get();
            
            $user_policy_list_id = Libraries\DBHelper::getListByTable("dx_users_accrual_policies")->id;
            $user_policy_field_id = DB::table('dx_lists_fields')
                             ->where('list_id', '=', $user_policy_list_id)
                             ->where('db_name', '=', 'user_id')
                             ->first()->id;
            
            foreach($timeoffs as $timeoff) {
                $balance = DB::table('dx_timeoff_calc')
                           ->where('user_id', '=', $this->id)
                           ->where('timeoff_type_id', '=', $timeoff->id)
                           ->orderBy('calc_date', 'DESC')
                           ->first();
                
                $timeoff->unit = trans('calendar.hours');
                $time = ($balance) ? $balance->balance : 0;
                if (!$timeoff->is_accrual_hours) {
                    $time = round(($time/Config::get('dx.working_day_h', 8)));
                    $timeoff->unit = trans('calendar.days');
                }
                
                $timeoff->balance = $time;
                $timeoff->user_policy_list_id = $user_policy_list_id;
                $timeoff->user_policy_field_id = $user_policy_field_id;
                
                $user_policy_row = DB::table('dx_users_accrual_policies')
                                ->where('user_id', '=', $this->id)
                                ->where('timeoff_type_id', '=', $timeoff->id)
                                ->whereNull('end_date')
                                ->first();
                
                $timeoff->user_policy_id = ($user_policy_row) ? $user_policy_row->id : 0;
                
            }
            
            return $timeoffs;            
	}
	
	/**
	 * Relation to country
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function country()
	{
		return $this->hasOne('App\Models\Country', 'id', 'country_id');
	}
	
	/**
	 * Relation to department
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function department()
	{
		return $this->hasOne('App\Models\Department', 'id', 'department_id');
	}
	
	/**
	 * Relation to manager
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function manager()
	{
		return $this->hasOne('App\User', 'id', 'manager_id');
	}
	
	/**
	 * Relation to team
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function team()
	{
		return $this->hasOne('App\Models\Team', 'id', 'team_id');
	}
	
	/**
	 * Relation to position
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function position()
	{
		return $this->hasOne('App\Models\Position', 'id', 'position_id');
	}
	
	/**
	 * Relation to team members
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function team_members()
	{
		return $this->hasMany('App\User', 'team_id', 'team_id');
	}
        
        /**
	 * Relation to subordinates (dirrect reporters)
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function subordinates()
	{
		return $this->hasMany('App\User', 'manager_id', 'id');
	}
        	
	/**
	 * Relation to offers for which user is subscribed.
	 */
	public function offers()
	{
		return $this->belongsToMany('App\Models\Offer', 'dx_users_offers', 'user_id', 'offer_id')
			->withPivot('quantity');
	}
	
	/**
	 * Get an URL of user's avatar
	 *
	 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
	 */
	public function getAvatar()
	{
		return url(\App\Libraries\Helper::getEmployeeAvatarBig($this->picture_guid));
	}
	
	public function getAvailability()
	{
		if($this->termination_date)
		{
			$result = [
				'button' => 'Left',
				'class' => 'grey',
				'title' => 'Employee has left'
			];
		}
		elseif($this->join_date && !$this->termination_date)
		{
			$result = [
				'button' => 'Active',
				'class' => 'green-jungle',
				'title' => 'Employee is at work'
			];
		}
		else
		{
			$result = [
				'button' => 'Potential',
				'class' => 'yellow-lemon',
				'title' => 'The person is in process of hiring'
			];
		}
		
		return $result;
	}
	
	/**
	 * Get an array of lists which the user has access to, and cache this array to $this->lists property.
	 *
	 * @return array
	 */
	public function getLists()
	{
		if($this->lists)
			return $this->lists;
		
		$lists = [];
		
		$this->roles->each(function ($role) use (&$lists)
		{
			$role->lists->each(function ($list) use (&$lists)
			{
				if(!isset($lists[$list->id]))
				{
					
					$lists[$list->id] = [
						'group' => $list->group_id,
						'new' => (boolean)$list->pivot->is_new_rights,
						'edit' => (boolean)$list->pivot->is_edit_rights,
						'delete' => (boolean)$list->pivot->is_delete_rigths
					];
				}
				
				else
				{
					$list->pivot->is_new_rights && ($lists[$list->id]['new'] = true);
					$list->pivot->is_edit_rights && ($lists[$list->id]['edit'] = true);
					$list->pivot->is_delete_rights && ($lists[$list->id]['delete'] = true);
				}
			});
		});
		
		$this->lists = $lists;
		
		return $this->lists;
	}
	
	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	protected static function boot()
	{
		parent::boot();
		
		// Add default user roles
		User::created(function ($user)
		{
			$roles = \App\Models\System\Role::where('is_default', true)->get();
			
			$user->roles()->attach($roles);
		});
	}
}

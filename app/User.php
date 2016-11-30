<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use DB;
use Log;

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
	 * List of users's notes
	 * @return App\Models\Employee\Note
	 */
	public function timeoffCalc()
	{
		return $this->hasMany('\App\Models\Employee\TimeoffCalc', 'user_id');
	}
        
        public function timeoffYears(){
            return DB::table('dx_timeoff_calc AS tc')
                ->select(DB::Raw('YEAR(tc.calc_date) as timeoffYear'))
                ->where('tc.user_id', $this->id)
                ->groupBy(DB::Raw('YEAR(tc.calc_date)'))
                ->orderBy(DB::Raw('YEAR(tc.calc_date)'), 'desc');
        }
        
        /**
	 * Users's timeoff data
	 * @return object Contains time off types data and value for specific user
	 */
	public function timeoff()
	{
            $timeoff_types = DB::table('dx_users_accrual_policies AS uap')                        
                ->select('uap.timeoff_type_id')   
                ->where('uap.user_id', $this->id)
                ->groupBy('uap.timeoff_type_id')
                ->lists('uap.timeoff_type_id');
            
            if(!$timeoff_types){
                return array();
            } 
            
            return DB::table('dx_timeoff_types AS tt')
                ->leftJoin('dx_timeoff_calc AS tc', 'tt.id', '=', 'tc.timeoff_type_id')
                ->leftJoin('dx_timeoff_calc AS tc2', function ($join) {
                    $join->on('tc2.timeoff_type_id', '=', 'tc.timeoff_type_id');
                    $join->on('tc2.user_id', '=', 'tc.user_id');
                    $join->on('tc2.calc_date', '>', 'tc.calc_date');
                })
                ->select('tt.id', 'tt.title', 'tt.icon', 'tt.color', 'tt.is_accrual_hours', 'tc.balance')
                ->whereNull('tc.id')
                ->orWhere(function ($query) use ($timeoff_types) {
                    $query->whereIn('tt.id', $timeoff_types)
                        ->where('tc.user_id', $this->id)
                        ->whereNull('tc2.calc_date');
                })
                ->orderBy('tt.title');
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
	 * Relation to team members
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function team_members()
	{
		return $this->hasMany('App\User', 'team_id', 'team_id');
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

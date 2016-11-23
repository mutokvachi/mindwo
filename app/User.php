<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

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
	 * @return App\Employee\EmployeePersonalDocument
	 */
	public function employeePersonalDocs()
	{
		return $this->hasMany('\App\Models\Employee\EmployeePersonalDocument', 'user_id');
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

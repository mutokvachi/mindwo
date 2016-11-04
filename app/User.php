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
    use Authenticatable, CanResetPassword;
    
     /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'dx_users';
    
    /**
     * Changes default column name for column updated_at 
     */
    const UPDATED_AT = 'modified_time';
    
    /**
     * Changes default column name for column created_at 
     */
    const CREATED_AT = 'created_time';  

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['login_name', 'display_name', 'position_title', 'picture_guid', 'email', 'password', 'id', 'is_blocked', 'updated_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
	
	public $timestamps = false;
	
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
	 * Relation to user roles
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function roles()
	{
		return $this->hasMany('App\Models\UserRoles', 'user_id', 'id');
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
            /*
            $thumb_path = 'formated_img/small_avatar/';
            $thumb = $thumb_path.$this->picture_guid;

            return is_file(public_path($thumb)) ? url($thumb) : url($thumb_path.get_portal_config('EMPLOYEE_AVATAR'));
            */
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
}

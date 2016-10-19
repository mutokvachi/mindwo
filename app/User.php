<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

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
	 * Relationship to user roles
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function roles()
	{
		return $this->hasMany('App\Models\UserRoles', 'user_id', 'id');
	}
	
	/**
	 * Relationship to country
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function country()
	{
		return $this->hasOne('App\Models\Country', 'id', 'country_id');
	}
	
	public function team()
	{
		return $this->hasMany('App\User', 'team_id', 'team_id');
	}
	
	/**
	 * Get URL of user's avatar
	 *
	 * @return \Illuminate\Contracts\Routing\UrlGenerator|string
	 */
	public function getAvatar()
	{
		$file = "formated_img/small_avatar/{$this->picture_guid}";
		
		return file_exists(public_path($file)) ? url($file) : get_portal_config('EMPLOYEE_AVATAR');
	}
	
	public function isAvailable()
	{
		
	}
}

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
    
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
        
        // Add default user roles
        User::created(function ($user) {
            $roles = \App\Models\System\Role::where('is_default', true)->get();
            
            $user->roles()->attach($roles); 
        });
    }
    
    /**
     * Get the comments for the blog post.
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\System\Role', 'dx_users_roles');
    }
}

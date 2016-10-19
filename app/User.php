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
    protected $fillable = ['login_name', 'display_name', 'position_title', 'picture_guid', 'email', 'password', 'id', 'is_blocked', 'updated_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
}

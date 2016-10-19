<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRoles extends Model
{
	/**
	 * @var string Table with user roles
	 */
	protected $table = 'dx_users_roles';

	/**
	 * @var bool Disable automatic timestamps assignment
	 */
	public $timestamps = false;
	
	public function user()
	{
		return $this->belongsTo('App\User', 'id', 'user_id');
	}
}

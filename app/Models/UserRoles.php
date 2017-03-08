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
	 * Changes default column name for column updated_at
	 */
	const UPDATED_AT = 'modified_time';
	
	/**
	 * Changes default column name for column created_at
	 */
	const CREATED_AT = 'created_time';
	
	/**
	 * Relation to user model
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo('App\User', 'id', 'user_id');
	}
}

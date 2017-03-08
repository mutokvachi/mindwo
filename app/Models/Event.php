<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
	/**
	 * Changes default column name for column updated_at
	 */
	const UPDATED_AT = 'modified_time';
	/**
	 * Changes default column name for column created_at
	 */
	const CREATED_AT = 'created_time';
	
	protected $table = 'dx_db_events';
	
	public function user()
	{
		return $this->hasOne('App\User', 'id', 'user_id');
	}
	
	public function type()
	{
		return $this->hasOne('App\Models\EventType', 'id', 'type_id');
	}
	
	public function lists()
	{
		return $this->hasOne('App\Models\Lists', 'id', 'list_id');
	}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lists extends Model
{
	/**
	 * Changes default column name for column updated_at
	 */
	const UPDATED_AT = 'modified_time';
	
	/**
	 * Changes default column name for column created_at
	 */
	const CREATED_AT = 'created_time';
	
    protected $table = 'dx_lists';
	
	public function group()
	{
		return $this->hasOne('App\Models\ListGroup', 'id', 'group_id');
	}
}

<?php

namespace App\Models\System;

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
	protected $fillable = [
		'list_title',
		'item_title',
		'object_id',
		'created_user_id',
		'modified_user_id'
	];
	
	public function group()
	{
		return $this->hasOne('App\Models\System\ListGroup', 'id', 'group_id');
	}
	
	public function fields()
	{
		return $this->hasMany('App\Models\System\ListField', 'list_id', 'id');
	}
	
	public function form()
	{
		return $this->hasOne('App\Models\System\Form', 'list_id', 'id');
	}
	
	public function views()
	{
		return $this->hasMany('App\Models\System\View', 'list_id', 'id');
	}
	
	public function roles()
	{
		return $this
			->belongsToMany('App\Models\System\Role', 'dx_roles_lists', 'list_id', 'role_id')
			->withPivot('id', 'is_new_rights', 'is_edit_rights', 'is_delete_rights');
	}
	
	public function roles_lists()
	{
		return $this->hasMany('App\Models\System\ListRole', 'list_id', 'id');
	}

	/**
	 * Related workflows
	 *
	 * @return App\Models\Workflow\Workflow Collection of workflows
	 */
	public function workflows()
	{
		return $this->hasMany('App\Models\Workflow\Workflow', 'list_id', 'id');
	}
}

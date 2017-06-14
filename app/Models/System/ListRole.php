<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class ListRole extends Model
{
	const UPDATED_AT = 'modified_time';
	const CREATED_AT = 'created_time';
	protected $table = 'dx_roles_lists';
	protected $fillable = [
		'role_id',
		'list_id',
		'is_edit_rights',
		'is_delete_rights',
		'is_new_rights',
		'is_import_rights',
		'is_view_rights',
		'created_user_id',
		'modified_user_id'
	];
}

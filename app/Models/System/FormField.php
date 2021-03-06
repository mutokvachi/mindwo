<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
	const UPDATED_AT = 'modified_time';
	const CREATED_AT = 'created_time';
	protected $table = 'dx_forms_fields';
	protected $fillable = [
		'list_id',
		'form_id',
		'field_id',
		'tab_id',
		'order_index',
		'group_label',
		'row_type_id',
		'created_user_id',
		'modified_user_id',
		'is_hidden'
	];
}

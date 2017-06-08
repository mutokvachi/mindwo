<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class ViewField extends Model
{
	const UPDATED_AT = 'modified_time';
	const CREATED_AT = 'created_time';
	protected $table = 'dx_views_fields';
        
        protected $fillable = [
		'list_id',
		'order_index',
		'field_id',
		'is_hidden',
	];
}

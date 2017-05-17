<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class ListField extends Model
{
	const UPDATED_AT = 'modified_time';
	const CREATED_AT = 'created_time';
	protected $table = 'dx_lists_fields';
}

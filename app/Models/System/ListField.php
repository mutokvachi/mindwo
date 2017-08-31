<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class ListField extends Model
{
	const UPDATED_AT = 'modified_time';
	const CREATED_AT = 'created_time';
	protected $table = 'dx_lists_fields';
	protected $fillable = [
		'db_name',
		'type_id',
		'title_list',
		'title_form',
		'rel_list_id',
		'rel_display_field_id',
		'default_value',
		'operation_id',
		'criteria'
	];

	/**
	 * Related list
	 *
	 * @return \App\System\List List model
	 */
	public function list()
    {
        return $this->belongsTo('\App\Models\System\Lists', 'list_id');
    }
}

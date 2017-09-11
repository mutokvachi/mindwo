<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class FormTab extends Model
{
	const UPDATED_AT = 'modified_time';
	const CREATED_AT = 'created_time';
	protected $table = 'dx_forms_tabs';
	protected $fillable = [
		'form_id',
		'title',
		'grid_list_id',
		'grid_list_field_id',
		'order_index',
		'created_user_id',
		'modified_user_id',
		'is_custom_data'
	];
	
	public function lists()
	{
		return $this->hasOne('App\Models\System\Lists', 'id', 'grid_list_id');
	}
	
	public function field()
	{
		return $this->hasOne('App\Models\System\ListField', 'id', 'grid_list_field_id');
	}
}

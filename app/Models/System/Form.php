<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
	const UPDATED_AT = 'modified_time';
	const CREATED_AT = 'created_time';
	protected $table = 'dx_forms';
	protected $fillable = ['title', 'form_type_id', 'created_user_id', 'modified_user_id'];
	
	public function fields()
	{
		return $this->hasMany('App\Models\System\FormField', 'form_id', 'id');
	}
	
	public function lists()
	{
		return $this->belongsTo('App\Models\System\Lists', 'id', 'list_id');
	}
}

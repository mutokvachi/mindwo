<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
	const UPDATED_AT = 'modified_time';
	const CREATED_AT = 'created_time';
	protected $table = 'dx_views';
	protected $fillable = ['title', 'view_type_id', 'is_default', 'created_user_id', 'modified_user_id'];
	
	public function fields()
	{
		return $this->hasMany('App\Models\System\ViewField', 'view_id', 'id');
	}
	
	public function lists()
	{
		return $this->belongsTo('App\Models\System\Lists', 'id', 'list_id');
	}
}
